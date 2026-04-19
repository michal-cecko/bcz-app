<?php

namespace App\Filament\Resources\Trainings\RelationManagers;

use App\Filament\Actions\SendEmailAction;
use App\Filament\Actions\SendEmailBulkAction;
use App\Models\Training;
use App\Services\EmailService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class WaitlistRelationManager extends RelationManager
{
    protected static string $relationship = 'waitlistEntries';

    protected static ?string $title = 'Čakací zoznam';

    protected static ?string $modelLabel = 'záznam';

    protected static ?string $pluralModelLabel = 'Čakací zoznam';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        /** @var Training $ownerRecord */
        return (bool) $ownerRecord->notify_on_available;
    }

    /**
     * @return list<array{email: string, variables: array<string, string>}>
     */
    protected function resolveWaitlistRecipient($record): array
    {
        $email = $record->user?->email;
        if (! $email) {
            return [];
        }

        /** @var Training $training */
        $training = $this->getOwnerRecord();
        $teamName = $training->team->getTranslation('name', 'sk');

        return [
            [
                'email' => $email,
                'variables' => [
                    'meno' => $record->user?->name ?? '',
                    'email' => $email,
                    'nazov_timu' => $teamName,
                    'nazov_treningu' => $training->getTranslation('title', 'sk'),
                    'miesto' => $training->getTranslation('place_name', 'sk') ?? '',
                    'cas' => $training->schedules->map(fn ($s) => ucfirst(mb_substr($s->day, 0, 2)).' '.($s->start_time ? Str::substr($s->start_time, 0, 5) : ''))->join(', ') ?: ($training->start_time ?? ''),
                    'kapacita' => (string) ($training->max_capacity ?? ''),
                ],
            ],
        ];
    }

    protected function makeWaitlistSendEmailAction(): Action
    {
        return Action::make('send_email_all')
            ->label('Odoslať e-mail všetkým')
            ->icon(Heroicon::OutlinedEnvelope)
            ->color('primary')
            ->slideOver()
            ->schema(fn (): array => array_merge(
                [$this->buildWaitlistRecipientsPlaceholder()],
                (new SendEmailAction('temp'))
                    ->contextVariables(['nazov_treningu', 'miesto', 'cas', 'kapacita'])
                    ->getEmailFormSchema(),
            ))
            ->modalSubmitActionLabel('Odoslať e-mail')
            ->modalSubmitAction(fn ($action) => $action->requiresConfirmation()
                ->modalHeading('Potvrdiť odoslanie')
                ->modalDescription('E-mail bude odoslaný všetkým na čakacom zozname.')
                ->modalSubmitActionLabel('Áno, odoslať'))
            ->action(function (array $data): void {
                $allRecipients = [];

                foreach ($this->getOwnerRecord()->waitlistEntries()->with('user')->get() as $entry) {
                    $allRecipients = array_merge($allRecipients, $this->resolveWaitlistRecipient($entry));
                }

                if (empty($allRecipients)) {
                    Notification::make()->warning()->title('Žiadni príjemcovia')->send();

                    return;
                }

                $count = EmailService::send(
                    subject: $data['subject'],
                    brickContent: $data['content'],
                    recipients: $allRecipients,
                    team: filament()->getTenant(),
                );

                Notification::make()
                    ->success()
                    ->title('E-mail odoslaný')
                    ->body("E-mail bol odoslaný {$count} príjemcom.")
                    ->send();
            });
    }

    protected function buildWaitlistRecipientsPlaceholder(): Placeholder
    {
        $emails = collect();
        foreach ($this->getOwnerRecord()->waitlistEntries()->with('user')->get() as $entry) {
            foreach ($this->resolveWaitlistRecipient($entry) as $r) {
                $emails->push($r['email']);
            }
        }
        $unique = $emails->unique()->values();
        $list = $unique->map(fn (string $e) => "<span style=\"display:inline-block;padding:2px 10px;margin:2px;border-radius:9999px;background:#e5e7eb;font-size:13px;\">{$e}</span>")->implode(' ');

        return Placeholder::make('recipients_info')
            ->label('Príjemcovia ('.$unique->count().')')
            ->content(new HtmlString($unique->isEmpty() ? '<span style="color:#9ca3af;">Žiadni príjemcovia</span>' : $list));
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Žiadni čakajúci')
            ->emptyStateDescription('Nikto zatiaľ nečaká na voľné miesto.')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Používateľ')
                    ->placeholder('Hosť')
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('E-mail')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Zapísaný')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                $this->makeWaitlistSendEmailAction(),
            ])
            ->recordActions([
                SendEmailAction::make('send_email')
                    ->contextVariables(['nazov_treningu', 'miesto', 'cas', 'kapacita'])
                    ->resolveRecipients(function ($record) {
                        return $this->resolveWaitlistRecipient($record);
                    }),
                DeleteAction::make()
                    ->modalHeading('Odstrániť z čakacieho zoznamu'),
            ])
            ->toolbarActions([
                SendEmailBulkAction::make('send_email_bulk')
                    ->contextVariables(['nazov_treningu', 'miesto', 'cas', 'kapacita'])
                    ->resolveRecipients(function ($record) {
                        return $this->resolveWaitlistRecipient($record);
                    }),
                DeleteBulkAction::make(),
            ]);
    }
}
