<div style="font-family: Arial, sans-serif; line-height:1.5;">
  <h2 style="margin:0 0 10px;">Tu boleto</h2>

  <p style="margin:0 0 10px;"><strong>Boleto:</strong> {{ $code }}</p>

  @if(!empty($messageText))
    <p style="margin:0 0 14px;">{{ $messageText }}</p>
  @endif

  <p style="margin:0 0 14px;">
    Abre tu boleto aquí:
    <br>
    <a href="{{ $publicUrl }}">{{ $publicUrl }}</a>
  </p>

  @if(!empty($qrUrl))
    <p style="margin:0 0 6px;">QR (opcional):</p>
    <p style="margin:0;"><a href="{{ $qrUrl }}">{{ $qrUrl }}</a></p>
  @endif

  <hr style="margin:18px 0; border:none; border-top:1px solid #ddd;">
  <p style="color:#666; font-size:12px; margin:0;">Este enlace puede expirar por seguridad.</p>
</div>
