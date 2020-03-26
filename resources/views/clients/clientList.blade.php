@extends('layouts.adminApp')

@section('content')
<div class="row" style="width: 100%;">
    <div class="col-sm-12">
        <table class="table policy-table">
            <thead>
            <tr>
                <th>id</th>
                <th>Имя</th>
                <th>Email</th>
                <th>Телефон</th>
                <th>Город</th>
                <th>Сфера деятельности</th>
                <th>Организация</th>
                <th>Должность</th>
                <th>Дата рождения</th>
                <th>Пол</th>
                <th>Образование</th>
                <th>Регион</th>
                <th>Дата создания</th>
            </tr>
            </thead>
            <tbody>
            @foreach($clients as $client)
            <?
            // $rating = $client->rating();
            ?>
            <tr>
                <td>{{ $client->id }}</td>
                <td>{{ $client->name }}</td>
                <td>{{ $client->email }}</td>
                <td>{{ $client->phone }}</td>
                <td>{{ $client->city }}</td>
                <td>{{ $client->field_of_activity }}</td>
                <td>{{ $client->organization }}</td>
                <td>{{ $client->position }}</td>
                <td>{{ $client->birthday }}</td>
                <td>{{ $client->sex }}</td>
                <td>{{ $client->education }}</td>
                <td>{{ $client->region }}</td>
                <td>{{ $client->created_at->timezone('Europe/Moscow') }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
$(document).ready(function() {

})
</script>
@endsection
