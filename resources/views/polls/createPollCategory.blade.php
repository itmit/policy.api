@extends('layouts.adminApp')

@section('content')
<div class="col-sm-9 tabs-content">
    <h1>Создание категории</h1>
    <div class="row">
        <div class="col-sm-12">
            <p class="title-poll">Уже созданные категории:</p>
            <div class="textareaPoll" name="listOfCategories" cols="20" rows="10" disabled style="resize: none;">
                @foreach ($categories as $category)
                @if($category->name == 'deleted')
                    @continue
                @endif
                <div class="category-item" style="display: flex;
                float: left;">
                {{ $category->name }} <i class="material-icons delete-category" style="cursor: pointer; align-items:center" data-category="{{ $category->id }}">delete</i>
                </div>
                @endforeach
            </div>
            <br>
            <br>
            <div class="title-poll">Создать категорию:</div>
            <form class="form-horizontal" method="POST" action="{{ route('auth.storePollCategory') }}" id="category-creating">
                {{ csrf_field() }}
                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <input id="name" type="text" class="form-control input-create-poll" name="name" value="{{ old('name') }}" required
                           autofocus placeholder="Наименование">
                    @if ($errors->has('name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group">
                    <button type="submit" class="btn-card">
                        Создать категорию
                    </button>
                </div>
            </form>
        </div>   
    </div> 
</div>

<script>
$('#category-creating').submit(function() {
    if($("input[name='name']").val() == 'deleted')
    {
        alert('Имя категории не может быть "deleted!"');
        return false;
    }
});
$(document).ready(function() {
    $(".textareaPoll").on("click", ".delete-category", function(e) {
        let isDelete = confirm("Удалить категорию?");

        if(isDelete)
        {
            let id = $(this).data('category');
            
            $.ajax({
                headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType: "json",
                data    : { id: id },
                url     : 'pollCategory/delete',
                method    : 'delete',
                success: function (response) {
                    $(this).closest(".category-item").remove();
                },
                error: function (xhr, err) { 
                    console.log("Error: " + xhr + " " + err);
                }
            });
        }
    });
})
</script>

@endsection