@extends('layouts.adminApp')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <a href="{{ route('auth.polls.create') }}" class="btn btn-primary">Создать опрос</a>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Наименование</th>
                    <th>Дата создания</th>
                </tr>
                </thead>
                <tbody>
                {{-- @foreach($clients as $client)
                    <tr>
                        <td><a href="client/{{ $client->id }}"> {{ $client->login }} </a></td>
                        <td>{{ $client->role }}</td>
                        <td>{{ $client->created_at->timezone('Europe/Moscow') }}</td>
                    </tr>
                @endforeach --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
