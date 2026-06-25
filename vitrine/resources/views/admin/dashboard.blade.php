@extends('layouts.admin')

@section('content')
<div class="admin__head">
  <h1 style="font-family:var(--font-display);font-size:36px;margin:0">Demandes de contact</h1>
  <div style="display:flex;gap:12px">
    @if (config('vitrine.umami_dashboard'))
      <a class="btn btn--secondary btn--sm" href="{{ config('vitrine.umami_dashboard') }}" target="_blank" rel="noopener">Voir les visites (Umami)</a>
    @endif
    <form method="POST" action="{{ route('admin.logout') }}" style="margin:0">
      @csrf
      <button type="submit" class="btn btn--ghost btn--sm">D&eacute;connexion</button>
    </form>
  </div>
</div>

@if ($messages->count())
  <table class="admin__table">
    <thead>
      <tr>
        <th>Date</th>
        <th>Nom</th>
        <th>Email</th>
        <th>Message</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($messages as $msg)
        <tr>
          <td>{{ $msg->created_at->format('d/m/Y H:i') }}</td>
          <td>{{ $msg->name }}</td>
          <td><a href="mailto:{{ $msg->email }}" style="color:var(--accent-soft);text-decoration:none">{{ $msg->email }}</a></td>
          <td title="{{ e($msg->message) }}">{{ Str::limit($msg->message, 80) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <div class="admin__pager">
    @if ($messages->previousPageUrl())
      <a class="btn btn--secondary btn--sm" href="{{ $messages->previousPageUrl() }}">&larr; Pr&eacute;c&eacute;dent</a>
    @endif
    @if ($messages->nextPageUrl())
      <a class="btn btn--secondary btn--sm" href="{{ $messages->nextPageUrl() }}">Suivant &rarr;</a>
    @endif
  </div>
@else
  <p style="color:var(--text-dim);font-size:14px">Aucune demande de contact pour le moment.</p>
@endif
@endsection
