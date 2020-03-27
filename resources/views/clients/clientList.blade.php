@extends('layouts.adminApp')

@section('content')
<div class="row" style="width: 100%;">
    <div class="col-sm-12">
        <table class="table policy-table">
            <thead>
            <tr>
                <th class="client-sort" style="cursor: pointer" data-sort-by="id" data-d="0">id</th>
                <th class="client-sort" style="cursor: pointer" data-sort-by="name" data-d="0">Имя</th>
                <th class="client-sort" style="cursor: pointer" data-sort-by="email" data-d="0">Email</th>
                <th class="client-sort" style="cursor: pointer" data-sort-by="phone" data-d="0">Телефон</th>
                <th class="client-sort" style="cursor: pointer" data-sort-by="city" data-d="0">Город</th>
                <th class="client-sort" style="cursor: pointer" data-sort-by="field_of_activity" data-d="0">Сфера деятельности</th>
                <th class="client-sort" style="cursor: pointer" data-sort-by="organization" data-d="0">Организация</th>
                <th class="client-sort" style="cursor: pointer" data-sort-by="position" data-d="0">Должность</th>
                <th class="client-sort" style="cursor: pointer" data-sort-by="birthday" data-d="0">Дата рождения</th>
                <th class="client-sort" style="cursor: pointer" data-sort-by="sex" data-d="0">Пол</th>
                <th class="client-sort" style="cursor: pointer" data-sort-by="education" data-d="0">Образование</th>
                <th class="client-sort" style="cursor: pointer" data-sort-by="region" data-d="0">Регион</th>
                <th class="client-sort" style="cursor: pointer" data-sort-by="created_at" data-d="0">Дата создания</th>
            </tr>
            </thead>
            <tbody>
            @foreach($clients as $client)
            <?
            // $rating = $client->rating();
            ?>
            <tr>
                <td>{{ $client->id }}</td>
                <td><a href="clients/{{ $client->id }}">{{ $client->name }}</a></td>
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
    $(document).on('click', '.client-sort', function() {
        $('.loader').css('display', 'block');
        let sortBy = $(this).data('sort-by');
        let direction = $(this).data('d');
        let elem = $(this);
        $.ajax({
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            dataType: "json",
            url     : 'clients/sort',
            data    : {sortBy: sortBy, direction: direction},
            method    : 'post',
            success: function (response) {
                $('tbody > tr').remove();
                result = '';
                response.forEach(element => {
                    if(element['name'] == null) element['name'] = '';
                    if(element['email'] == null) element['email'] = '';
                    if(element['phone'] == null) element['phone'] = '';
                    if(element['city'] == null) element['city'] = '';
                    if(element['field_of_activity'] == null) element['field_of_activity'] = '';
                    if(element['organization'] == null) element['organization'] = '';
                    if(element['position'] == null) element['position'] = '';
                    if(element['birthday'] == null) element['birthday'] = '';
                    if(element['sex'] == null) element['sex'] = '';
                    if(element['education'] == null) element['education'] = '';
                    if(element['region'] == null) element['region'] = '';
                    if(element['created_at'] == null) element['created_at'] = '';
                    result += '<tr>';
                    result += '<td>'+element['id']+'</td>';
                    result += '<td><a href="clients/'+element['id']+'">'+element['name']+'</a></td>';
                    result += '<td>'+element['email']+'</td>';
                    result += '<td>'+element['phone']+'</td>';
                    result += '<td>'+element['city']+'</td>';
                    result += '<td>'+element['field_of_activity']+'</td>';
                    result += '<td>'+element['organization']+'</td>';
                    result += '<td>'+element['position']+'</td>';
                    result += '<td>'+element['birthday']+'</td>';
                    result += '<td>'+element['sex']+'</td>';
                    result += '<td>'+element['education']+'</td>';
                    result += '<td>'+element['region']+'</td>';
                    result += '<td>'+element['created_at']+'</td>';
                    result += '</tr>';
                });
                $('tbody').html(result);

                switch (elem.data('d')) {
                    case 0:
                        elem.data('d', 1);
                        break;
                    case 1:
                        elem.data('d', 0);
                        break;
                    default:
                        break;
                }

                $('.loader').css('display', 'none');
            },
            error: function (xhr, err) { 
                console.log("Error: " + xhr + " " + err);
            }
        });
    });
})
</script>
@endsection
