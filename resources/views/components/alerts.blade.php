@props(['status' => null, 'error' => null])

@php
    $status = $status ?? session('status');
    $error = $error ?? session('error');
    $hasErrors = $errors->any();
@endphp

@if ($status || $error || $hasErrors)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if ($status)
                window.Alerts.success('{{ addslashes($status) }}');
            @endif

            @if ($error)
                window.Alerts.error('{{ addslashes($error) }}');
            @endif

            @if ($hasErrors)
                window.Alerts.validation(@json($errors->all()));
            @endif
        });
    </script>
@endif
