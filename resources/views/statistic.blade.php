<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1024">
</head>
<style>
    ul.column {
        list-style: none;
        width: 50px;
        height: 100%;
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
                @foreach ($votes as $date => $vote)
                <ul class="column" id='column' @if($vote['count']==$max) style="height: 100%" @else <?php $percent = $vote['count'] / $max * 100 ?> @endif>
                    <li class="Quantity">
                        <span id="quantity">{{ $vote['count'] }}</span>
                    </li>
                    <li class="cl-green">
                        <span id="like">{{ round($vote['likes']) }}</span>
                    </li>
                    <li class="cl-gray ">
                        <span id="neit">{{ round($vote['neutrals']) }}</span>
                    </li>
                    <li class="cl-red">
                        <span id="dis">{{ round($vote['dislikes']) }}</span>
                    </li>
                    <li class="Daytime">
                        <span>
                            {{ $date }}
                        </span>
                    </li>
                </ul>
                @endforeach
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
<script type="text/javascript">
    // let arr = [10, 5, 5];

    // let summ = arr[0] + arr[1] + arr[2];

    // let percentage = summ / ((summ + 5 + 5 + 6 + 1 + 1 + 2) * 1 / 100);

    // document.getElementById('columnOne').style.height = percentage + "%";

    // let like = arr[0] / (summ * 1 / 100);
    // let neit = arr[1] / (summ * 1 / 100);
    // let dis = arr[2] / (summ * 1 / 100);

    // document.getElementById('quantity').innerHTML = summ;
    // document.getElementById('like').innerHTML = like + '%';
    // document.getElementById('like').parentNode.style.height = like + '%';
    // document.getElementById('neit').innerHTML = neit + '%';
    // document.getElementById('neit').parentNode.style.height = neit + '%';
    // document.getElementById('dis').innerHTML = dis + '%';
    // document.getElementById('dis').parentNode.style.height = dis + '%';

</script>

</html>
