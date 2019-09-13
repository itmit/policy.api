@extends('layouts.adminApp')

@section('content')
    <h1>Создание опроса</h1>
    <div class="col-sm-12">
        <form class="form-horizontal" method="POST" action="{{ route('auth.polls.store') }}">
            {{ csrf_field() }}

            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name" class="col-md-4 control-label">Наименование</label>

                <div class="col-md-6">
                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required
                           autofocus>

                    @if ($errors->has('name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('radio') ? ' has-error' : '' }}">
                <div class="col-md-6">
                    <input type="radio" id="unlimited" name="time" value="unlimited" checked>
                    <label for="unlimited">Бессрочно</label>

                    <input type="radio" id="limited" name="time" value="limited">
                    <label for="limited">с датой начала и завершения</label>
                </div>
            </div>

            <div class="form-group{{ $errors->has('start_at') ? ' has-error' : '' }}">
                <label for="start_at" class="col-md-4 control-label">Дата начала</label>

                <div class="col-md-6">
                    <input id="start_at" type="date" class="form-control" name="start_at" value="{{ old('start_at') }}"
                           autofocus disabled>

                    @if ($errors->has('start_at'))
                        <span class="help-block">
                            <strong>{{ $errors->first('start_at') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('end_at') ? ' has-error' : '' }}">
                <label for="end_at" class="col-md-4 control-label">Дата завершения</label>

                <div class="col-md-6">
                    <input id="end_at" type="date" class="form-control" name="end_at" value="{{ old('end_at') }}"
                           autofocus disabled>

                    @if ($errors->has('end_at'))
                        <span class="help-block">
                            <strong>{{ $errors->first('end_at') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <hr>
            <h2>Создание вопросов</h2>

            <div class="list_of_questions">
                <div class="form-group" name="questions" data-questions="0">
                    <div class="col-md-4">
                        <input id="question" data-question="0" type="text" class="form-control" name="question" placeholder=" Введите вопрос">
                    </div>
                    <div class="col-md-5 offset-md-1">
                        <input id="answer" type="text" data-answer="0" class="form-control" name="answer" placeholder=" Введите ответ">    
                        <input id="answer" type="text" data-answer="1" class="form-control" name="answer" placeholder=" Введите ответ">
                        <input type="checkbox" name="other" id="other">
                        <label for="other">Включает вариант ответа "другой"</label>
                        <button>Добавить вариант ответа</button>
                    </div>
                </div>

                <div class="form-group" name="questions" data-questions="1">
                    <div class="col-md-4">
                        <input id="question" data-question="1" type="text" class="form-control" name="question" placeholder=" Введите вопрос">
                    </div>
                    <div class="col-md-5 offset-md-1">
                        <input id="answer" type="text" data-answer="0" class="form-control" name="answer" placeholder=" Введите ответ">    
                        <input id="answer" type="text" data-answer="1" class="form-control" name="answer" placeholder=" Введите ответ">
                        <input type="checkbox" name="other" id="other">
                        <label for="other">Включает вариант ответа "другой"</label>
                        <button>Добавить вариант ответа</button>
                    </div>
                </div>

            </div>

            <button>Добавить вопрос</button>


            <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                    <button type="submit" class="btn btn-primary">
                        Создать опрос
                    </button>
                </div>
            </div>
        </form>
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
    })
    </script>
    
@endsection