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
        margin: 0px 15px;
        padding: 0;
        position: relative;
        min-height: 20%;
        box-shadow: 5px 0px 9px 0px #777777;
    }

    li.Quantity {
        position: absolute;
        top: -20px;
        font-size: 18px;
        width: 100%;
    }

    li.cl-green {
        background: #6fc372;
        color: white;
        height: 20%;
        transition-duration: 1s;
        /*border-radius: 10px 10px 0px 0px;*/
    }

    li.cl-gray {
        background: #8c8c8c;
        color: white;
        height: 50%;
        transition-duration: 1s;
    }

    li.cl-red {
        background: #ff2525;
        color: white;
        height: 30%;
        transition-duration: 1s;
    }

    ul.column span {
        display: flex;
        justify-content: center;
        height: 100%;
        align-items: center;
    }

    .horizontal-columns-section {
        display: flex;
        border-bottom: 1px solid black;
        margin: 40px 0px 0 10px;
        height: 300px;
        align-items: flex-end;
    }

    .legend {
        background: #f0f0f0;
        border-radius: 4px;
        width: 500px;
        list-style: none;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 30px 20px;
    }

    .legend li {
        display: block;
        margin: 0;
        padding: 20px 30px;
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

</style>

<body>
    <div id="wrapper">
        <div class="chart">
            <div class="horizontal-columns-section">
                <ul class="column" id='columnOne'>
                    <li class="Quantity">
                        <span id="quantity">50</span>
                    </li>
                    <li class="cl-green">
                        <span id="like">20%</span>
                    </li>
                    <li class="cl-gray">
                        <span id="neit">50%</span>
                    </li>
                    <li class="cl-red">
                        <span id="dis">30%</span>
                    </li>
                    <li class="Daytime">
                        <span>
                            {{ strtotime($lastSevenDays[0]->created_at) }}
                        </span>
                    </li>
                </ul>
                <ul class="column">
                    <li class="Quantity">
                        <span>20</span>
                    </li>
                    <li class="cl-green">
                        <span>20%</span>
                    </li>
                    <li class="cl-gray">
                        <span>50%</span>
                    </li>
                    <li class="cl-red">
                        <span>30%</span>
                    </li>
                    <li class="Daytime">
                        <span>
                            2
                        </span>
                    </li>
                </ul>
                <ul class="column">
                    <li class="Quantity">
                        <span>10</span>
                    </li>
                    <li class="cl-green">
                        <span>20%</span>
                    </li>
                    <li class="cl-gray">
                        <span>50%</span>
                    </li>
                    <li class="cl-red">
                        <span>30%</span>
                    </li>
                    <li class="Daytime">
                        <span>
                            3
                        </span>
                    </li>
                </ul>
                <ul class="column">
                    <li class="Quantity">
                        <span>10</span>
                    </li>
                    <li class="cl-green">
                        <span>20%</span>
                    </li>
                    <li class="cl-gray">
                        <span>50%</span>
                    </li>
                    <li class="cl-red">
                        <span>30%</span>
                    </li>
                    <li class="Daytime">
                        <span>
                            4
                        </span>
                    </li>
                </ul>
                <ul class="column">
                    <li class="Quantity">
                        <span>10</span>
                    </li>
                    <li class="cl-green">
                        <span>20%</span>
                    </li>
                    <li class="cl-gray">
                        <span>50%</span>
                    </li>
                    <li class="cl-red">
                        <span>30%</span>
                    </li>
                    <li class="Daytime">
                        <span>
                            5
                        </span>
                    </li>
                </ul>
                <ul class="column">
                    <li class="Quantity">
                        <span>10</span>
                    </li>
                    <li class="cl-green">
                        <span>20%</span>
                    </li>
                    <li class="cl-gray">
                        <span>50%</span>
                    </li>
                    <li class="cl-red">
                        <span>30%</span>
                    </li>
                    <li class="Daytime">
                        <span>
                            6
                        </span>
                    </li>
                </ul>
                <ul class="column">
                    <li class="Quantity">
                        <span>10</span>
                    </li>
                    <li class="cl-green">
                        <span>20%</span>
                    </li>
                    <li class="cl-gray">
                        <span>50%</span>
                    </li>
                    <li class="cl-red">
                        <span>30%</span>
                    </li>
                    <li class="Daytime">
                        <span>
                            7
                        </span>
                    </li>
                </ul>
            </div>
        </div>
        <ul class="legend">
            <li><span class="icon fig0"></span>Лайки</li>
            <li><span class="icon fig1"></span>Нейтралные</li>
            <li><span class="icon fig2"></span>Дизлайки</li>
        </ul>
    </div>
</body>
<script type="text/javascript">
    let arr = [10, 5, 5];

    let summ = arr[0] + arr[1] + arr[2];

    let percentage = summ / ((summ + 5 + 5 + 6 + 1 + 1 + 2) * 1 / 100);

    document.getElementById('columnOne').style.height = percentage + "%";

    let like = arr[0] / (summ * 1 / 100);
    let neit = arr[1] / (summ * 1 / 100);
    let dis = arr[2] / (summ * 1 / 100);

    document.getElementById('quantity').innerHTML = summ;
    document.getElementById('like').innerHTML = like + '%';
    document.getElementById('like').parentNode.style.height = like + '%';
    document.getElementById('neit').innerHTML = neit + '%';
    document.getElementById('neit').parentNode.style.height = neit + '%';
    document.getElementById('dis').innerHTML = dis + '%';
    document.getElementById('dis').parentNode.style.height = dis + '%';

</script>

</html>
