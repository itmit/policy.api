@extends('layouts.adminApp')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <a href="{{ route('auth.susliks.create') }}" class="btn btn-primary">Создать суслика</a>
            <a href="{{ route('suslik/createCategory') }}" class="btn btn-primary">Создать категорию</a>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Имя</th>
                    <th>Дата создания</th>
                </tr>
                </thead>
                <tbody>
                @foreach($susliks as $suslik)
                    <tr>
                        <td><a href="suslik/{{ $suslik->id }}"> {{ $suslik->name }} </a></td>
                        <td>{{ $suslik->created_at->timezone('Europe/Moscow') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
