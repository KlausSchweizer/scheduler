@extends('layouts.main-layout')

@section('content')
    <div class="d-flex flex-column justify-content-center align-items-center text-center mt-5">
        <img alt="sunrise" src="{{ asset('images/day_and_night.svg') }}" width="20%"
             style="transform: rotate({{ Cache::get('global_theme', 'light') == 'light' ? '36' : '216' }}deg); transition: transform 0.5s ease;">

        <div class="mt-4">
            <h1>
                @if(Cache::get('global_theme', 'light') == 'light')
                    Agora está de dia
                @else
                    Agora está de noite
                @endif
            </h1>
        </div>
    </div>

    <hr class="my-5">

    <h3 class="mb-3">Tarefas Ativas</h3>
    <div class="row">
        <div class="col-md-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">Nova Tarefa</div>
                <div class="card-body">
                    <form action="{{ route('tasks.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="title" class="form-control"
                                   placeholder="Digite o título da tarefa..." required>
                        </div>
                        <button type="submit" class="btn btn-primary">Adicionar Tarefa</button>
                    </form>
                </div>
            </div>

            <h3 class="mb-3">Tarefas Ativas</h3>

            <div class="row">
                @foreach($tasks as $task)
                    <div class="col-12 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-1">{{ $task->title }}</h5>
                                    @if($task->description)
                                        <p class="card-text text-muted mb-0 small">{{ $task->description }}</p>
                                    @endif
                                </div>
                                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="mb-0">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Marcar como feita
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                @endforeach
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm border-danger mb-4">
                <div class="card-header bg-danger text-white">
                    Lixeira/Tarefas resolvidas
                    <span class="badge bg-light text-dark float-end">{{ $trashedTasks->count() }}</span>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">
                        Itens na lixeira são removidos automaticamente após 30 dias.
                    </p>
                </div>
            </div>

            <div class="row">
                @foreach($trashedTasks as $trashed)
                    <div class="col-12 mb-3">
                        <div class="card shadow-sm border-danger">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-1 text-danger text-decoration-line-through">{{ $trashed->title }}</h5>
                                    <p class="card-text text-muted mb-0" style="font-size: 0.75rem;">Excluído
                                        em: {{ $trashed->deleted_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <form action="{{ route('tasks.force-delete', $trashed->id) }}" method="POST"
                                      class="mb-0">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
