@extends('layouts.admin')

@section('content')
<form class="admin__login" method="POST" action="">
  @csrf
  <h1>Admin</h1>

  @if ($errors->any())
    <div class="contact__alert contact__alert--err" style="padding:12px 16px;background:var(--risk-inacc-bg);border:1px solid var(--risk-inacc);border-radius:var(--r-sm);font-size:13px;color:var(--text)">
      {{ $errors->first('password') }}
    </div>
  @endif

  <label class="label">Mot de passe</label>
  <input class="input" type="password" name="password" required autofocus>

  <button type="submit" class="btn btn--accent btn--lg" style="align-self:flex-start">Se connecter</button>
</form>
@endsection
