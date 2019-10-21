@extends('layouts.adminApp')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <a href="{{ route('auth.susliks.create') }}" class="btn btn-primary">Создать суслика</a>
            <a href="{{ route('auth.createCategory') }}" class="btn btn-primary">Создать категорию</a>
            <a href="{{ route('auth.uploadSusliks') }}" class="btn btn-primary">Загрузить сусликов из csv-файла</a>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Имя</th>
                    <th>Категория</th>
                    <th>Дата создания</th>
                </tr>
                </thead>
                <tbody>
                @foreach($susliks as $suslik)
                    <tr>
                        <td><a href="suslik/{{ $suslik->id }}"> {{ $suslik->name }} </a></td>
                        <td>{{ $suslik->category()->name }}</td>
                        <td>{{ $suslik->created_at->timezone('Europe/Moscow') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
