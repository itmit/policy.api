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
        <div class="form-group">
            <label for="description" class="control-label">Описание: </label>
            <div id="description">{{ $poll->description }}</div>
        </div>
        <div class="form-group">
            <label for="category_name" class="control-label">Категория: </label>
            <div id="category_name">{{ $poll->category()->name }}</div>
        </div>

    </div>
</div>

</div>
@endsection