@extends('layouts.main-layout')

@section('content')
    <div class="d-flex flex-column justify-content-center align-items-center text-center mt-5">
        <img alt="sunrise" src="{{ asset('images/day_and_night.svg') }}" width="30%"
             style="transform: rotate({{ Cache::get('global_theme', 'light') == 'light' ? '36' : '216' }}deg); transition: transform 0.5s ease;">

        <div class="mt-4">
            <h1>
                @if(Cache::get('global_theme', 'light') == 'light')
                    Agora está de dia
                @else
                    Agora está de noite
                @endif
            </h1>
            <p class="text-muted">Estado atualizado pelo Scheduler do Laravel.</p>
        </div>
    </div>
@endsection
