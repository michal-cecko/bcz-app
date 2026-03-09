<h2>Nová správa z kontaktného formulára</h2>

<table cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%;">
    <tr>
        <td style="font-weight: bold; vertical-align: top; padding: 8px; border-bottom: 1px solid #eee;">Meno:</td>
        <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $inquiry->name }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold; vertical-align: top; padding: 8px; border-bottom: 1px solid #eee;">E-mail:</td>
        <td style="padding: 8px; border-bottom: 1px solid #eee;"><a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a></td>
    </tr>
    @if($inquiry->phone)
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding: 8px; border-bottom: 1px solid #eee;">Telefón:</td>
            <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $inquiry->phone }}</td>
        </tr>
    @endif
    @if($inquiry->reason)
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding: 8px; border-bottom: 1px solid #eee;">Dôvod:</td>
            <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $inquiry->reason->getLabel() }}</td>
        </tr>
    @endif
    <tr>
        <td style="font-weight: bold; vertical-align: top; padding: 8px;">Správa:</td>
        <td style="padding: 8px;">{!! nl2br(e($inquiry->message)) !!}</td>
    </tr>
</table>
