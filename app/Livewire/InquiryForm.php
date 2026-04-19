<?php

namespace App\Livewire;

use App\Enums\InquiryReasonEnum;
use App\Enums\InquiryStatusEnum;
use App\Mail\InquiryReceivedMail;
use App\Models\Inquiry;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class InquiryForm extends Component
{
    public bool $submitted = false;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $message = '';

    public string $reason = '';

    public bool $gdprAgreed = false;

    public function mount(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone ?? '';
        }
    }

    /** @return array<string, string[]> */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:5000'],
            'reason' => ['nullable', 'string'],
            'gdprAgreed' => ['accepted'],
        ];
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
            'name.required' => 'Meno je povinné.',
            'name.max' => 'Meno môže mať maximálne 255 znakov.',
            'email.required' => 'E-mail je povinný.',
            'email.email' => 'Zadajte platnú e-mailovú adresu.',
            'email.max' => 'E-mail môže mať maximálne 255 znakov.',
            'phone.max' => 'Telefón môže mať maximálne 50 znakov.',
            'message.required' => 'Správa je povinná.',
            'message.max' => 'Správa môže mať maximálne 5000 znakov.',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        $inquiry = Inquiry::create([
            'team_id' => Setting::get('default_team_id'),
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'message' => $this->message,
            'reason' => $this->reason ? InquiryReasonEnum::from($this->reason) : InquiryReasonEnum::OTHER,
            'status' => InquiryStatusEnum::NEW,
        ]);

        Mail::send(new InquiryReceivedMail($inquiry));

        $this->submitted = true;
    }

    public function render(): View
    {
        return view('livewire.inquiry-form', [
            'reasons' => InquiryReasonEnum::cases(),
        ]);
    }
}
