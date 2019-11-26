@extends('layouts.adminApp')

@section('content')
<div class="col-sm-9 tabs-content">
    <h1>Опрос {{ $poll->name }}</h1>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <label for="created_at" class="control-label">Создан: </label>
            <div id="created_at">{{ $poll->created_at }}</div>
        </div>
        @if($poll->start_at != NULL && $poll->end_at != NULL)
            <div class="form-group">
                <label for="description" class="control-label">Дата начала и завершения: </label>
                <div id="description">{{ $poll->start_at }} {{ $poll->end_at }}</div>
            </div>
        @endif
        @if($poll->link != NULL)
            <div class="form-group">
                <label for="link" class="control-label">Дата начала и завершения: </label>
                <div id="link">{{ $poll->link }} {{ $poll->end_at }}</div>
            </div>
        @endif
        @if($poll->description != NULL)
        <div class="form-group">
            <label for="description" class="control-label">Описание: </label>
            <div id="description">{{ $poll->description }}</div>
        </div>
        @endif
        <div class="form-group">
            <label for="category_name" class="control-label">Категория: </label>
            <div id="category_name">{{ $poll->category()->name }}</div>
        </div>
    </div>
</div>

</div>
<div class="row">
    <div class="col-sm-12">
        <table class="table policy-table">
                <thead>
                    <tr>
                        <th scope="col">№</th>
                        <th scope="col">пол (1-м, 2-ж)</th>
                        <th scope="col">Год рождения</th>
                        <th scope="col">Образование (1-высшее, 2-ср.специальное, 3-среднее полное, 4-неполное среднее или ниже)</th>
                        <th scope="col">Субъект РФ</th>
                        <th scope="col">ID</th>
                        <th scope="col">Имя/ник</th>
                        <?php $i=1;?>
                        @foreach ($questions as $question)
                            <th scope="col">V{{$i}}</th>
                            <?php $i++;?>
                        @endforeach
                    </tr>
                </thead>
        </table>
    </div>
</div>
@endsection