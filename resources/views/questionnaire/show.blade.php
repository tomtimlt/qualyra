<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('usages.index') }}" style="color: inherit; text-decoration: none">Cervus / Mes usages IA</a>
        / <a href="{{ route('usages.show', $aiUsage) }}" style="color: inherit; text-decoration: none">{{ \Illuminate\Support\Str::limit($aiUsage->name, 30) }}</a>
        / <b>Questionnaire</b>
    </x-slot>

    <div class="quest-page">
        <div class="quest-page__head">
            <div class="eyebrow eyebrow--accent">Questionnaire AI Act · {{ $aiUsage->type }}</div>
            <h1>{{ $aiUsage->name }}</h1>
            <p class="lead">
                Vos réponses permettent de classer cet usage selon les niveaux de risque définis par l'AI Act.
                Tout est sauvegardé en base : vous pouvez interrompre et reprendre plus tard.
            </p>
        </div>

        <form method="POST" action="{{ route('usages.questionnaire.store', $aiUsage) }}" class="quest-form">
            @csrf

            @foreach ($questions as $question)
                @php
                    $key = $question['key'];
                    $name = "answers[{$key}]";
                    if (($question['type'] ?? null) === 'checkbox') {
                        $stored = $answers[$key] ?? '';
                        $defaults = $stored === '' ? [] : array_filter(explode(',', $stored));
                        $current = old("answers.{$key}", $defaults);
                        if (! is_array($current)) {
                            $current = [];
                        }
                    } else {
                        $current = old("answers.{$key}", $answers[$key] ?? null);
                    }
                @endphp

                <div class="quest-block">
                    <div class="quest-block__head">
                        <label for="{{ $key }}" class="label">{{ $question['label'] }}{!! ($question['required'] ?? false) ? ' <span style="color:var(--accent)">*</span>' : '' !!}</label>
                        @if (! empty($question['help']))
                            <p class="help">{{ $question['help'] }}</p>
                        @endif
                    </div>

                    @if ($question['type'] === 'textarea')
                        <textarea id="{{ $key }}" name="{{ $name }}" rows="4" class="input textarea"
                                  @if ($question['required'] ?? false) required @endif>{{ $current }}</textarea>

                    @elseif ($question['type'] === 'select')
                        <select id="{{ $key }}" name="{{ $name }}" class="input"
                                @if ($question['required'] ?? false) required @endif>
                            <option value="">— Sélectionner —</option>
                            @foreach ($question['options'] as $value => $label)
                                <option value="{{ $value }}" @selected($current === $value)>{{ $label }}</option>
                            @endforeach
                        </select>

                    @elseif ($question['type'] === 'radio')
                        <div class="check-list">
                            @foreach ($question['options'] as $value => $label)
                                <label class="check-row {{ $current === $value ? 'is-on' : '' }}">
                                    <input type="radio" name="{{ $name }}" value="{{ $value }}"
                                           @checked($current === $value)
                                           @if ($question['required'] ?? false) required @endif>
                                    <div class="check-row__dot"></div>
                                    <div class="check-row__label">{{ $label }}</div>
                                </label>
                            @endforeach
                        </div>

                    @elseif ($question['type'] === 'checkbox')
                        <div class="check-list">
                            @foreach ($question['options'] as $value => $label)
                                @php $isOn = in_array($value, $current, true); @endphp
                                <label class="check-row check-row--box {{ $isOn ? 'is-on' : '' }}">
                                    <input type="checkbox" name="answers[{{ $key }}][]" value="{{ $value }}"
                                           @checked($isOn)>
                                    <div class="check-row__box"></div>
                                    <div class="check-row__label">{{ $label }}</div>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <x-input-error :messages="$errors->get('answers.'.$key)" />
                </div>
            @endforeach

            <div class="quest-actions">
                <a href="{{ route('usages.show', $aiUsage) }}" class="btn-link">← Annuler</a>
                <x-primary-button>Enregistrer mes réponses</x-primary-button>
            </div>
        </form>
    </div>

    <style>
        h1 { font-family: var(--font-display); font-size: 48px; line-height: 1.05; letter-spacing: -0.02em; margin: 8px 0 0; color: var(--text); }

        .quest-page { max-width: 760px; }
        .quest-page__head { margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px solid var(--hairline); }
        .quest-page__head .lead { margin-top: 16px; max-width: 64ch; }

        .quest-form { display: flex; flex-direction: column; gap: 32px; }

        .quest-block { padding: 24px 28px; border: 1px solid var(--hairline); border-radius: var(--r-md); background: var(--ink-950); display: flex; flex-direction: column; gap: 16px; }
        .quest-block__head { display: flex; flex-direction: column; gap: 4px; }
        .quest-block__head .label { font-size: 14px; font-weight: 500; color: var(--text); margin-bottom: 0; line-height: 1.4; }
        .quest-block__head .help { font-size: 12px; color: var(--text-dim); margin: 0; line-height: 1.55; }

        .quest-block .input { font-size: 14px; }
        .quest-block .textarea { min-height: 88px; }

        .check-list { display: flex; flex-direction: column; gap: 8px; }
        .check-row { display: flex; align-items: flex-start; gap: 12px; padding: 12px 14px; border: 1px solid var(--hairline); border-radius: var(--r-sm); background: var(--ink-1000); cursor: pointer; transition: all var(--d-fast) var(--ease-out); }
        .check-row:hover { border-color: var(--ink-500); }
        .check-row.is-on { border-color: var(--accent); background: rgba(46, 95, 160, 0.06); }
        .check-row input { position: absolute; opacity: 0; pointer-events: none; }
        .check-row__dot, .check-row__box { width: 16px; height: 16px; border: 1px solid var(--ink-500); flex-shrink: 0; margin-top: 1px; position: relative; transition: all var(--d-fast); }
        .check-row__dot { border-radius: 50%; }
        .check-row__box { border-radius: 2px; }
        .check-row.is-on .check-row__dot, .check-row.is-on .check-row__box { border-color: var(--accent); }
        .check-row.is-on .check-row__dot::after { content: ''; position: absolute; inset: 3px; border-radius: 50%; background: var(--accent); }
        .check-row.is-on .check-row__box::after { content: '✓'; position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 12px; line-height: 1; font-weight: 700; }
        .check-row__label { font-size: 13px; color: var(--text); line-height: 1.5; }

        .quest-actions { display: flex; align-items: center; justify-content: flex-end; gap: 16px; padding-top: 16px; }
        .btn-link { color: var(--text-muted); font-size: 13px; text-decoration: none; transition: color var(--d-fast); }
        .btn-link:hover { color: var(--text); }

        @media (max-width: 720px) {
            h1 { font-size: 36px; }
        }
    </style>
</x-app-layout>
