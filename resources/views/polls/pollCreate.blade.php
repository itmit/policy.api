@extends('layouts.adminApp')

@section('content')
<div class="col-sm-9 tabs-content">
    <h1>Создание опроса</h1>
<div class="row">
    <div class="col-sm-12">
        <form class="form-horizontal" method="POST" action="{{ route('auth.polls.store') }}">
            {{ csrf_field() }}

            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name" class="control-label">Наименование:</label>
                <input id="name" type="text" class="form-control input-create-poll" name="name" value="{{ old('name') }}" required
                       autofocus>

                @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                <label for="description" class="control-label">Описание:</label>
                <textarea name="description" id="description" cols="30" rows="10" style="resize: none" class="form-control textareapoll" placeholder=" Необязательно"></textarea>

                @if ($errors->has('description'))
                    <span class="help-block">
                        <strong>{{ $errors->first('description') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                <label for="category" class="control-label">Категория опроса:</label>
                <select name="category" id="category" class="form-control selectpoll" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id}}">{{ $category->name}}</option>
                    @endforeach
                </select>

                @if ($errors->has('category'))
                    <span class="help-block">
                        <strong>{{ $errors->first('category') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('link') ? ' has-error' : '' }}">
                <label for="link" class="control-label">Ссылка на опрос:</label>
                <input type="text" name="link" id="link" class="form-control input-create-poll" value="{{ old('link') }}" placeholder=" Необязательно">

                @if ($errors->has('link'))
                    <span class="help-block">
                        <strong>{{ $errors->first('link') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('radio') ? ' has-error' : '' }}">
                    <input type="radio" id="unlimited input-create-poll" name="time" value="unlimited" checked>
                    <label for="unlimited">Бессрочно</label>

                    <input type="radio" id="limited input-create-poll" name="time" value="limited">
                    <label for="limited">с датой начала и завершения</label>
            </div>

            <div class="form-group{{ $errors->has('start_at') ? ' has-error' : '' }}">
                <label for="start_at" class="control-label">Дата начала:</label>
                <input id="start_at" type="date" class="form-control input-create-poll" name="start_at" value="{{ old('start_at') }}"
                       autofocus disabled>

                @if ($errors->has('start_at'))
                    <span class="help-block">
                        <strong>{{ $errors->first('start_at') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('end_at') ? ' has-error' : '' }}">
                <label for="end_at" class="control-label">Дата завершения:</label>
                <input id="end_at" type="date" class="form-control input-create-poll" name="end_at" value="{{ old('end_at') }}"
                       autofocus disabled>

                @if ($errors->has('end_at'))
                    <span class="help-block">
                        <strong>{{ $errors->first('end_at') }}</strong>
                    </span>
                @endif
            </div>

            <hr>
            <h2>Создание вопросов</h2>

            <div class="list_of_questions">
                <div class="question">
                    <div class="block-var">
                        <div class="question_name">
                            <input type="text" name="question_name" placeholder=" Вопрос" class="form-control input-create-poll-1" required>
                        </div>
                        <div class="question_option_multiple">
                            <input type="checkbox" name="multiple"> Множественный
                        </div>
                        <div class="question_option_other">
                            <input type="checkbox" name="other"> Включает вариант ответа "другой"
                        </div>
                    </div>
                    <div class="answers_container">
                        <div class="answers">
                            <div class="answer offset-md-1">
                                <input type="text" name="answer" placeholder=" Ответ" class="form-control input-create-poll" required> <input type="text" name="answer_count" placeholder=" Количество ответивших" class="form-control input-create-poll" required> 
                            </div>
                            <div class="answer offset-md-1">
                                <input type="text" name="answer" placeholder=" Ответ" class="form-control input-create-poll" required> <input type="text" name="answer_count" placeholder=" Количество ответивших" class="form-control input-create-poll" required> 
                            </div>
                        </div>
                        <div class="add-answer offset-md-1">
                            <input type="button" value="Добавить ответ" class="add_answer btn-apply">
                        </div>
                    </div>
                </div>
            </div>
            <p>
                <input type="button" value="Добавить вопрос" class="add_new_question btn-apply">
            </p>
            <div class="form-group">
                <div class="col-md-offset-4">
                    {{-- <input type="button" value="Создать опрос" class="test btn-card"> --}}
                    <button type="submit" class="test btn-card">Создать опрос</button>
                </div>
            </div>
        </form>
    </div>
</div>
    <script>
    $(document).ready(function() {
        $(document).on('change', $('input:radio[name=time]'), function() {
            if($('input:radio[name=time]:checked').val() == 'limited')
            {
                $("#start_at").prop("disabled", false);
                $("#end_at").prop("disabled", false);

                $("#start_at").prop("required", true);
                $("#end_at").prop("required", true);
            }
            if($('input:radio[name=time]:checked').val() == 'unlimited')
            {
                $("#start_at").prop("disabled", true);
                $("#end_at").prop("disabled", true);

                $("#start_at").prop("required", false);
                $("#end_at").prop("required", false);
            }

        });

        $(".add_new_question").on("click", function() {
            $('.list_of_questions').append(sergay.content.cloneNode(true));
        });

        $(".list_of_questions").on("click", ".delete_question", function(e) {
            $(this).closest(".question").remove();
        });

        $(".list_of_questions").on("click", ".add_answer", function(e) {
            // let elem = document.createElement('div');
            // elem.append(radik.content.cloneNode(true));
            $(this).closest(".answers_container").find('.answers').append(radik.content.cloneNode(true));
            // $(this).closest(".answers_container").find('.answers').append('new');
        });

        $(".list_of_questions").on("click", ".delete-answer", function(e) {
            $(this).closest(".row").remove();
        });

        // $(document).on("click", ".test", function(e) {
        $("form").submit(function(e) {

            let all_data = new Map([
            ['name', $("input[name='name']").val()],
            ['description', $("[name='description']").val()],
            ['category', $("[name='category']").val()],
            ['link', $("input[name='link']").val()]
            ]);  

            if($('input:radio[name=time]:checked').val() == 'limited')
            {
                all_data.set('start_at', $("input[name='start_at']").val());
                all_data.set('end_at', $("input[name='end_at']").val());
            }
            if($('input:radio[name=time]:checked').val() == 'unlimited')
            {
                all_data.set('start_at', null);
                all_data.set('end_at', null);
            }

            let question_number = 0;

            let all_questions = new Map();
            $( ".question" ).each(function( index ) {
                let question_data = new Map(); 
                let answer_data = new Map();  
                let answer_count = new Map();  

                question_data.set('question_name', $(this).find("input[name='question_name']").val());
                question_data.set('multiple', $(this).find("input[name='multiple']").prop('checked'));
                question_data.set('other', $(this).find("input[name='other']").prop('checked'));

                let i = 0;
                $(this).find('.answer').each(function( index ) {
                    answer_data.set(i, $(this).find("input[name='answer']").val());
                    answer_count.set(i, $(this).find("input[name='answer_count']").val());
                    console.log(answer_count);
                    i++;
                });

                answer_data = Object.fromEntries(answer_data)
                answer_count = Object.fromEntries(answer_count)
                question_data.set('answers', answer_data);
                // question_data.set('answer_count', answer_count);
                question_data = Object.fromEntries(question_data)
                // data.set('question'+question_number, question_data);
                all_questions.set('question'+question_number, question_data);
                
                question_number++;
            });

            all_questions = Object.fromEntries(all_questions)
            all_data.set('questions', all_questions);
            const all_data_array = Object.fromEntries(all_data);

            // $.ajax({
            //     headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            //     dataType: "html",
            //     data    : {all_data : all_data_array},
            //     url     : '../polls',
            //     method    : 'post',
            //     success: function (response) {
            //         console.log(response);
            //     },
            //     error: function (xhr, err) { 
            //         console.log("Error: " + xhr + " " + err);
            //     }
            // });
            // window.location.replace("http://policy.itmit-studio.ru/polls");
            return false;
        });

    })
    </script>

    <style>
        .question {
            margin-bottom: 10px;
        }
        .material-icons {
            cursor: pointer;
            padding-top: 7px;
        }
        input {
            margin-bottom: 10px;
        }
    </style>

    <template id="sergay">
        <div class="question">
            <hr>
            <div class="block-var">
                <div class="question_name">
                    <input type="text" name="question_name" placeholder=" Вопрос" class="form-control input-create-poll-1" required>
                </div>
                <div class="question_option_multiple">
                    <input type="checkbox" name="multiple"> Множественный
                </div>
                <div class="question_option_other">
                    <input type="checkbox" name="other"> Включает вариант ответа "другой"
                </div>
            </div>
            <div class="answers_container">
                <div class="answers">
                    <div class="answer offset-md-1">
                        <input type="text" name="answer" placeholder=" Ответ" class="form-control input-create-poll" required> <input type="text" name="answer_count" placeholder=" Количество ответивших" class="form-control input-create-poll" required> 
                    </div>
                    <div class="answer offset-md-1">
                        <input type="text" name="answer" placeholder=" Ответ" class="form-control input-create-poll" required> <input type="text" name="answer_count" placeholder=" Количество ответивших" class="form-control input-create-poll" required> 
                    </div>
                </div>
                <div class="add-answer offset-md-1">
                    <input type="button" value="Добавить ответ" class="add_answer btn-apply">
                </div>
            </div>
            <div>
                <input type="button" value="Удалить вопрос" class="col-md-4 delete_question btn-apply">
            </div>
        </div>
    </template>

    <template id="radik">
        <div class="row">
            <div class="answer col-md-4 offset-md-1">
                <input type="text" name="answer" placeholder=" Ответ" class="form-control input-create-poll" required> <input type="text" name="answer_count" placeholder=" Количество ответивших" class="form-control input-create-poll" required> 
            </div>
            <div class="answer-delete col-md-2">
                <i class="material-icons delete-answer">delete</i>
            </div>
        </div>
    </template>
</div>
@endsection