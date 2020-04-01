@extends('layouts.adminApp')

@section('content')
<div class="col-sm-9 tabs-content">
    <div class="row justify-content-center cont-m">
        <div class="col-md-12">
           <div class="group-btn-card">
                <a href="{{ route('auth.susliks.create') }}" class="btn-card">Создать суслика</a>
                <a href="{{ route('auth.createCategory') }}" class="btn-card">Создать категорию</a>
            </div>
            <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('auth.uploadSusliks') }}">
                {{ csrf_field() }}

                <br>

                <div class="row form-group{{ $errors->has('file') ? ' has-error' : '' }}">

                    <label for="file" class="col-md-4 form-control-file">.zip-папка для импорта</label>
        
                    <div class="col-md-6">
                        <input type="file" name="file" id="file2" accept=".zip">
                    </div>
        
                    @if ($errors->has('file'))
                        <span class="help-block">
                            <strong>{{ $errors->first('file') }}</strong>
                        </span>
                    @endif
                </div>
        
                <div class="form-group">
                    <button type="submit" class="btn-card btn-tc-ct">
                            Загрузить сусликов из .zip
                    </button>
                </div>
            </form>

            <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('auth.uploadSusliksJSON') }}">
                {{ csrf_field() }}

                <br>

                <div class="row form-group{{ $errors->has('file') ? ' has-error' : '' }}">
                    <label for="file" class="col-md-4 form-control-file">.json файл для импорта</label>
        
                    <div class="col-md-6">
                        <input type="file" name="file" id="file" accept=".json">
                        <select name="category" class="form-control">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
        
                    @if ($errors->has('file'))
                        <span class="help-block">
                            <strong>{{ $errors->first('file') }}</strong>
                        </span>
                    @endif
                </div>
        
                <div class="form-group">
                    <button type="submit" class="btn-card btn-tc-ct">
                            Загрузить сусликов из .json
                    </button>
                </div>
            </form>

            <button type="button" class="btn-card btn-tc-danger js-destroy-button">Удалить отмеченных сусликов</button>
            {{-- <button type="button" class="btn-card btn-tc-danger js-clear-dir-button">Очистить каталог</button> --}}
        </div>
    </div>
</div>
<div class="row" style="width: 100%;">
    <div class="col-sm-12">
        <select name="category" class="form-control suslik-by-category">
            <option value="" selected disabled>Выберите категорию для просмотра</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        <table class="table policy-table" id="table">
            <thead>
            <tr>
                <th><input type="checkbox" name="destroy-all-susliks" class="js-destroy-all"/></th>
                <th class="suslik-sort" style="cursor: pointer" data-sort-by="name" data-d="0">Имя</th>
                {{-- <th>Категория</th> --}}
                <th class="suslik-sort" style="cursor: pointer" data-sort-by="place_of_work" data-d="0">Место работы</th>
                <th class="suslik-sort" style="cursor: pointer" data-sort-by="position" data-d="0">Должность</th>
                <th class="suslik-sort" style="cursor: pointer" style="cursor: pointer" data-sort-by="link" data-d="0">Ссылка</th>
                <th class="suslik-sort" style="cursor: pointer" data-sort-by="likes" data-d="0">Лайки</th>
                <th class="suslik-sort" style="cursor: pointer" data-sort-by="dislikes" data-d="0">Дизлайки</th>
                <th class="suslik-sort" style="cursor: pointer" data-sort-by="neutrals" data-d="0">Нейтралы</th>
                {{-- <th>Дата создания</th> --}}
                <th><span class="material-icons">create</span></th>
            </tr>
            </thead>
            <tbody>
            {{-- @foreach($susliks as $suslik)
            @if($suslik->category() == NULL)
            @continue
            @endif
                <tr>
                    <td><input type="checkbox" data-suslik-id="{{ $suslik->id }}" name="destoy-suslik-{{ $suslik->id }}" class="js-destroy"/></td>
                    <td>{{ $suslik->name }}</td>
                    <td>{{ $suslik->category()->name }}</td>
                    <td>{{ $suslik->place_of_work }}</td>
                    <td>{{ $suslik->position }}</td>
                    <td><a href="{{ $suslik->link }}" target="_blank">ссылка</a></td>
                    <td>{{ $suslik->likes }}</td>
                    <td>{{ $suslik->dislikes }}</td>
                    <td>{{ $suslik->neutrals }}</td>
                    <td>{{ $suslik->created_at->timezone('Europe/Moscow') }}</td>
                    <td><span class="material-icons"><a href="susliks/{{ $suslik->id }}/edit">create</a></span></td>
                </tr>
            @endforeach --}}
            </tbody>
        </table>
    </div>
</div>
<script>
$(document).ready(function() {
    let arrayOfSusliks;
    $(function(){
        $(".js-destroy-all").on("click", function() {

            if($(".js-destroy-all").prop("checked")){
                $(".js-destroy").prop("checked", "checked");
            }
            else{
                $(".js-destroy").prop("checked", "");
            }
        });
    });

    $(document).on('click', '.js-destroy-button', function() {
        let ids = [];

        $(".js-destroy:checked").each(function(){
            ids.push($(this).data('suslikId'));
        });

        $.ajax({
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            dataType: "json",
            data    : { ids: ids },
            url     : 'susliks/delete',
            method    : 'delete',
            success: function (response) {
                console.log(response);
                $(".js-destroy:checked").closest('tr').remove();
                $(".js-destroy").prop("checked", "");
            },
            error: function (xhr, err) { 
                console.log("Error: " + xhr + " " + err);
            }
        });

    });

    $(document).on('click', '.js-clear-dir-button', function() {
        $.ajax({
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            dataType: "json",
            url     : 'susliks/clearDir',
            method    : 'get',
            success: function (response) {
                alert('Директория очищена!')
            },
            error: function (xhr, err) { 
                console.log("Error: " + xhr + " " + err);
            }
        });

    });

    $(document).on('change', '.suslik-by-category', function() {
        $('.loader').css('display', 'block');
        let category = $(this).children("option:selected").val();
        $.ajax({
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            dataType: "json",
            url     : 'susliks/getSusliksByCategory',
            data    : {category: category},
            method    : 'post',
            success: function (response) {
                arrayOfSusliks = response;
                result = '';
                response.forEach(element => {
                    if(element['place_of_work'] == null) element['place_of_work'] = '';
                    if(element['position'] == null) element['position'] = '';
                    result += '<tr>';
                    result += '<td><input type="checkbox" data-suslik-id="'+element['id']+'" name="destoy-suslik-'+element['id']+'" class="js-destroy"/></td>';
                    result += '<td>'+element['name']+'</td>';
                    result += '<td>'+element['place_of_work']+'</td>';
                    result += '<td>'+element['position']+'</td>';
                    result += '<td><a href="'+element['link']+'" target="_blank">ссылка</a></td>';
                    result += '<td>'+element['likes']+'</td>';
                    result += '<td>'+element['dislikes']+'</td>';
                    result += '<td>'+element['neutrals']+'</td>';
                    result += '<td><span class="material-icons"><a href="susliks/'+element['id']+'/edit">create</a></span></td>';
                    result += '</tr>';
                });
                result += '<tr><td colspan="9" style="text-align: center; cursor: pointer" name="load-more" data-c="50">загрузить еще</td></tr>';
                $('tbody').html(result);
                $('.loader').css('display', 'none');
            },
            error: function (xhr, err) { 
                console.log("Error: " + xhr + " " + err);
            }
        });
    });

    $(document).on('click', '.suslik-sort', function() {
        $('.loader').css('display', 'block');
        let sortBy = $(this).data('sort-by');
        let category = $('.suslik-by-category').children("option:selected").val();
        let direction = $(this).data('d');
        $('th').data('d', 0);
        let elem = $(this);
        let c = $('td[name="load-more"]').data("c");
        $.ajax({
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            dataType: "json",
            url     : 'susliks/sort',
            data    : {category: category, sortBy: sortBy, direction: direction, c: c},
            method    : 'post',
            success: function (response) {
                $('tbody > tr').remove();
                result = '';
                response.forEach(element => {
                    if(element['place_of_work'] == null) element['place_of_work'] = '';
                    if(element['position'] == null) element['position'] = '';
                    result += '<tr>';
                    result += '<td><input type="checkbox" data-suslik-id="'+element['id']+'" name="destoy-suslik-'+element['id']+'" class="js-destroy"/></td>';
                    result += '<td>'+element['name']+'</td>';
                    result += '<td>'+element['place_of_work']+'</td>';
                    result += '<td>'+element['position']+'</td>';
                    result += '<td><a href="'+element['link']+'" target="_blank">ссылка</a></td>';
                    result += '<td>'+element['likes']+'</td>';
                    result += '<td>'+element['dislikes']+'</td>';
                    result += '<td>'+element['neutrals']+'</td>';
                    result += '<td><span class="material-icons"><a href="susliks/'+element['id']+'/edit">create</a></span></td>';
                    result += '</tr>';
                });
                result += '<tr><td colspan="9" style="text-align: center; cursor: pointer" name="load-more" data-c="'+c+'">загрузить еще</td></tr>';
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

    $(document).on('click', 'td[name="load-more"]', function() {
        $('.loader').css('display', 'block');
        let category = $('.suslik-by-category').children("option:selected").val();
        let c = $('td[name="load-more"]').data("c");
        let newC = c + 50;
        let sortBy = $('th[data-d="1"]').data('sort-by');
        let direction = $('th[data-d="1"]').data('d');
        console.log(category + ' ' + newC + ' ' + sortBy + ' ' + direction);
        // $.ajax({
        //     headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        //     dataType: "json",
        //     url     : 'susliks/loadMore',
        //     data    : {category: category, sortBy: sortBy, direction: direction, c: c},
        //     method    : 'post',
        //     success: function (response) {
        //         $('tbody > tr').remove();
        //         result = '';
        //         response.forEach(element => {
        //             if(element['place_of_work'] == null) element['place_of_work'] = '';
        //             if(element['position'] == null) element['position'] = '';
        //             result += '<tr>';
        //             result += '<td><input type="checkbox" data-suslik-id="'+element['id']+'" name="destoy-suslik-'+element['id']+'" class="js-destroy"/></td>';
        //             result += '<td>'+element['name']+'</td>';
        //             result += '<td>'+element['place_of_work']+'</td>';
        //             result += '<td>'+element['position']+'</td>';
        //             result += '<td><a href="'+element['link']+'" target="_blank">ссылка</a></td>';
        //             result += '<td>'+element['likes']+'</td>';
        //             result += '<td>'+element['dislikes']+'</td>';
        //             result += '<td>'+element['neutrals']+'</td>';
        //             result += '<td><span class="material-icons"><a href="susliks/'+element['id']+'/edit">create</a></span></td>';
        //             result += '</tr>';
        //         });
        //         result += '<tr><td colspan="9" style="text-align: center; cursor: pointer" name="load-more" data-c="'+c+'">загрузить еще</td></tr>';
        //         $('tbody').html(result);

        //         switch (elem.data('d')) {
        //             case 0:
        //                 elem.data('d', 1);
        //                 break;
        //             case 1:
        //                 elem.data('d', 0);
        //                 break;
        //             default:
        //                 break;
        //         }

        //         $('.loader').css('display', 'none');
        //     },
        //     error: function (xhr, err) { 
        //         console.log("Error: " + xhr + " " + err);
        //     }
        // });
    })
})
</script>



@endsection
