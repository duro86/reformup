<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial; }
        .btn {
            display:inline-block;
            padding:10px 15px;
            background:#4CAF50;
            color:white;
            text-decoration:none;
            border-radius:5px;
        }
    </style>
</head>
<body>

<h2>¡Tu perfil ha sido validado!</h2>

<p>Hola {{ $user->nombre }},</p>

<p>
    Tu perfil profesional <strong>{{ $perfil->empresa }}</strong>
    ha sido validado por el equipo de ReformUp.
</p>

<p><a href="{{ route('profesional.dashboard') }}" class="btn">Acceder a tu panel</a></p>

<p>Gracias por confiar en ReformUp.</p>

</body>
</html>
