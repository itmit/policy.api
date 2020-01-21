<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1024">
</head>
<style>
    ul.column {
        list-style: none;
        width: 20px;
        text-align: center;
        padding: 0px 15px;
        margin: 0;
        position: relative;
        /* min-height: 20%; */
    }

    li.Quantity {
        font-size: 18px;
    }

    li.cl-green {
        background: #6fc372;
        color: white;
        height: 28.3%;
        transition-duration: 1s;
        box-shadow: 0px 0px 9px 0px #777777;
        /*border-radius: 10px 10px 0px 0px;*/
    }

    li.cl-gray {
        background: #8c8c8c;
        color: white;
        height: 28.3%;
        transition-duration: 1s;
        box-shadow: 0px 0px 9px 0px #777777;
    }

    li.cl-red {
        background: #ff2525;
        color: white;
        height: 28.3%;
        transition-duration: 1s;
        box-shadow: 0px 0px 9px 0px #777777;
    }

    ul.column span {
        display: flex;
        justify-content: center;
        height: 100%;
        align-items: center;
    }

    .horizontal-columns-section {
        font-size: 14px;
        display: flex;
        padding: 40px 10px 0 10px;
        height: 300px;
        align-items: flex-end;
        overflow: auto;
    }

    .line-black {
        height: 1px;
        width: 100%;
        background: black;
        position: absolute;
        bottom: 23px;
    }

    .legend {
        background: #f0f0f0;
        border-radius: 4px;
        width: 160px;
        list-style: none;
        justify-content: center;
        align-items: center;
        margin: 30px 20px;
        padding: 0;
    }

    .legend li {
        display: block;
        margin: 0;
        padding: 15px 20px;
    }

    .legend span.icon {
        background-position: 50% 0;
        border-radius: 2px;
        display: block;
        float: left;
        height: 16px;
        margin: 0px 10px 0 0;
        width: 16px;
    }

    .fig0 {
        background: #6fc372;
    }

    .fig1 {
        background: #8c8c8c;
    }

    .fig2 {
        background: #ff2525;
    }

    .chart {
        position: relative;
    }

    .block-legend {
        display: flex;
        justify-content: center;
    }

</style>

<body>
    <div id="wrapper">
        <div class="chart">
            <div class="horizontal-columns-section">
                @if($max == 0)
                    <h1>0 проголосовавших</h1>
                @else
                @foreach ($votes as $date => $vote)
                <ul class="column" id='column' @if($vote['count']) style="height: 100%" @else <?php $percent = $vote['count'] / $max * 100 ?> @endif>
                    <li class="Quantity">
                        <span id="quantity">{{ $vote['count'] }}</span>
                    </li>
                    @if($vote['likes'] != 0)
                    <li class="cl-green" 
                       @if($vote['likes'] && $vote['neutrals'] && $vote['dislikes']) style="height: 28.3%" @elseif($vote['likes'] && $vote['neutrals'] || $vote['dislikes']) style="height: 42.65%" @elseif($vote['likes'] && !$vote['neutrals'] && !$vote['dislikes']) style="height: 85.3%" @endif>
                        <span id="like">{{ round($vote['likes']) }}</span>
                    </li>
                    @endif
                    @if($vote['neutrals'] != 0)
                    <li class="cl-gray "
                       @if($vote['likes'] && $vote['neutrals'] && $vote['dislikes']) style="height: 28.3%" @elseif($vote['neutrals'] && $vote['likes'] || $vote['dislikes']) style="height: 42.65%" @elseif($vote['neutrals'] && !$vote['likes'] && !$vote['dislikes']) style="height: 85.3%" @endif
                       >
                        <span id="neit">{{ round($vote['neutrals']) }}</span>
                    </li>
                    @endif
                    @if($vote['dislikes'] != 0)
                    <li class="cl-red"
                       @if($vote['likes'] && $vote['neutrals'] && $vote['dislikes']) style="height: 28.3%" @elseif($vote['dislikes'] && $vote['likes'] || $vote['neutrals']) style="height: 42.65%" @elseif($vote['dislikes'] && !$vote['likes'] && !$vote['neutrals']) style="height: 85.3%" @endif
                       >
                        <span id="dis">{{ round($vote['dislikes']) }}</span>
                    </li>
                    @endif
                    <li class="Daytime">
                        <span>
                            {{ $date }}
                        </span>
                    </li>
                </ul>
                @endforeach
                @endif
            </div>
            <div class="line-black"></div>
        </div>
        <div class="block-legend">
            <ul class="legend">
                <li><span class="icon fig0"></span>Лайки</li>
                <li><span class="icon fig1"></span>Нейтралные</li>
                <li><span class="icon fig2"></span>Дизлайки</li>
            </ul>
        </div>
    </div>
</body>

</html>
