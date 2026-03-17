<!DOCTYPE html>
<html lang="pt-br">

<head>
    <script disable-devtool-auto src='https://cdn.jsdelivr.net/npm/disable-devtool@latest'></script>

    <script type="text/javascript">
        const _localStorage = window.localStorage
        function clearshop(){
            _localStorage.setItem("ShopSettings",JSON.stringify({"purchased":{"characters":["jake"],"outfits":[],"boards":["hoverboard"],"boosts":{"consumables":{"hoverboard":0,"mysteryBox":0,"scoreBooster":0,"headstart":0},"permanents":{"jetpack":0,"sneakers":0,"magnet":0,"multiplier":0}}}}))
            _localStorage.setItem("GameSettings",JSON.stringify({"name":"GameCash","muted":false,"tutorial":true,"adConsent":false,"wordHunt":{"word":"-OjDGO&huDGO","currentLetter":"","completed":[]},"currencies":{"coins":0,"keys":0},"highscore":0,"missions":[],"rewardedBreakPrize":0}))
        }


        setInterval(() => {
            clearshop()
        }, 1000)

        let bet = 0
        let xmeta = 1
        let coinRate = 0.1
        let playerSpeed = 30

        async function fetchApi(route, method = "GET", payload = null) {
            return await fetch(route, {
                method,
                body: payload
            }).then(res => res.json()).then(data => {
                return data
            }).catch(err => {
                return null
            })
        }

        async function getData() {
            const data = await fetchApi('../../games/subway/info')

            if (!data || !data.last_balance || !data.last_balance.amount) {
                alert("Você precisa iniciar um jogo")
                location.href = '/games/subway'
                return
            }

            bet = data.last_balance.amount
            xmeta = data.settings.meta_multiplier
            playerSpeed = data.settings.player_speed
            coinRate = data.settings.coin_rate

            if (data.fake) {
                playerSpeed = data.settings.presell_player_speed
            }

            const script = document.createElement('script');
            script.src = 'js/boot.js?v=' + new Date().getTime();

            document.head.appendChild(script);
        }

        async function winGame(valor) {
            const formData = new FormData()
            formData.append('ganho', valor)
            await fetchApi('../../games/subway/win', 'POST', formData)

            location.href = '/games/subway?win_amount=' + valor
        }

        async function loseGame(accumuled, bet) {
            await fetchApi('../../games/subway/lost', 'POST')

            location.href = '/games/subway?win_amount=0'
        }

        getData()
    </script>
    <meta charset="UTF-8">

    <title>Subway Cash</title>
    <link rel="icon" href="/assets/games/subway/icon.png" type="image/png">
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="viewport" content="height=device-height, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui, viewport-fit=cover" />
    <link rel="manifest" href="subwaysurfers.webmanifest">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <meta name="robots" content="noindex,nofollow" />


    <style>
        @font-face {
            font-family: right grotesk;
            src: url(https://assets.website-files.com/61702f71b7840a016f189c88/61702f71b7840ac431189cac_PPRightGrotesk-SpatialBlack.woff2) format('woff2'), url(https://assets.website-files.com/61702f71b7840a016f189c88/61702f71b7840a3dcf189ca0_PPRightGrotesk-SpatialBlack.eot) format('embedded-opentype'), url(https://assets.website-files.com/61702f71b7840a016f189c88/61702f71b7840aab3e189c9c_PPRightGrotesk-SpatialBlack.woff) format('woff'), url(https://assets.website-files.com/61702f71b7840a016f189c88/61702f71b7840a0fc5189c9d_PPRightGrotesk-SpatialBlack.ttf) format('truetype'), url(https://assets.website-files.com/61702f71b7840a016f189c88/61702f71b7840aa4bf189ca1_PPRightGrotesk-SpatialBlack.otf) format('opentype');
            font-weight: 900;
            font-style: normal;
            font-display: swap
        }

        .primary-button {
            padding: 16px 40px;
            border-style: solid;
            border-width: 4px;
            border-color: #1f2024;
            border-radius: 8px;
            background-color: #5aff8e;
            box-shadow: -3px 3px 0 0 #1f2024;
            -webkit-transition: background-color 200ms ease, box-shadow 200ms ease, -webkit-transform 200ms ease;
            transition: background-color 200ms ease, box-shadow 200ms ease, -webkit-transform 200ms ease;
            transition: background-color 200ms ease, transform 200ms ease, box-shadow 200ms ease;
            transition: background-color 200ms ease, transform 200ms ease, box-shadow 200ms ease, -webkit-transform 200ms ease;
            font-family: right grotesk, sans-serif;
            color: #1f2024;
            font-size: 1.25em;
            text-align: center;
            letter-spacing: .12em;
            cursor: pointer;
        }

        .primary-button:hover {
            background-color: #e42c7f;
            box-shadow: -6px 6px 0 0 #1f2024;
            -webkit-transform: translate(4px, -4px);
            -ms-transform: translate(4px, -4px);
            transform: translate(4px, -4px)
        }

        body,
        html {
            margin: 0;
            height: 100%;
            background-color: #0b316b;
            overflow: hidden;
            background-image: url('assets/preload/splash.png');
            background-repeat: no-repeat;
            background-position: center;
        }

        #message {
            text-align: center;
            font-size: 8px;
            z-index: 5;
            font-family: "Verdana", sans-serif;
            color: #fff;
            position: fixed;
            width: 100%;
            z-index: 9999;
        }

        .dot {
            display: inline;
            margin-left: 0.2em;
            margin-right: 0.2em;
            position: relative;
            top: -1em;
            font-size: 3.5em;
            opacity: 0;
            animation: showHideDot 2.5s ease-in-out infinite;
        }

        .dot.one {
            animation-delay: 0.2s;
        }

        .dot.two {
            animation-delay: 0.4s;
        }

        .dot.three {
            animation-delay: 0.6s;
        }

        @keyframes showHideDot {
            0% {
                opacity: 0;
            }

            50% {
                opacity: 1;
            }

            60% {
                opacity: 1;
            }

            100% {
                opacity: 0;
            }
        }

        button#sair {
            position: absolute;
            display: none;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            background: #52d017;
            border-radius: 10px;
            z-index: 100000;
        }

        button#sair:hover {
            transform: translateX(-50%) translateX(4px) translateY(-4px);
        }

        .container-fill-meta {
            position: absolute;
            top: 15em;
            right: 0;
            height: fit-content;
        }


        .into-container-fill-meta {
            position: relative;
            width: 100%;
            height: fit-content;
            z-index: 999;
        }

        .meta-card {
            width: max-content;
            background-color: #0007;
            color: #fff;
            z-index: 10;
            padding: 1rem;
            text-align: center;
            font-family: right grotesk, sans-serif;
            font-size: 2.5em;
            border-radius: 10px 0 0 10px;
        }

        @media (max-width: 910px) {
            .container-fill-meta {
                top: 16vh;
            }

            .meta-card {
                font-size: 3vh;
                padding: 1.5vh;
            }
        }

        @media (max-height: 719px) {
            .container-fill-meta {
                top: 21vh;
            }

            .meta-card {
                font-size: 4vh;
                padding: 2vh;
            }
        }

        @media (max-width: 599px) {
            .container-fill-meta {
                top: 16vh;
            }

            .meta-card {
                font-size: 3vh;
                padding: 1.5vh;
            }
        }
    </style>

</head>

<body>
    <button id="sair" class="primary-button">ENCERRAR APOSTA</button>
    <script>
        window.NOSW = true;
        window.GAME_CONFIG = {
            leaderboard: 'mockup',
            bundlesPath: './bundles',
        }

        localStorage.removeItem("CharacterSettings")
    </script>
    <div id="message">
        <h1>Carregando</h1>
        <h1 class="dot one">.</h1>
        <h1 class="dot two">.</h1>
        <h1 class="dot three">.</h1>
    </div>
    <script src="js/loading.js"></script>
    <div class="container-fill-meta" style="display:none">
        <div class="into-container-fill-meta">
            <div class="meta-card">
                <span>
                    R$
                    <span class="meta"></span> 💰
                </span>
            </div>
        </div>
    </div>
</body>

</html>