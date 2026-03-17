<!DOCTYPE html>
<html>
<head>
    <script disable-devtool-auto src='https://cdn.jsdelivr.net/npm/disable-devtool@latest'></script>
    <title>DinoWin</title>
    <link rel="icon" href="/assets/games/dino/icon.png" />

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script src="https://game-cdn.poki.com/scripts/v2/poki-sdk.js"></script>
    <script src="https://game-cdn.poki.com/scripts/a7c3d8457e9d001550c049f821d50d7c386f7a05/poki-sdk-hoist-a7c3d8457e9d001550c049f821d50d7c386f7a05.js" type="text/javascript" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <style>
    body {
      background-color: #f7f7f7;
    }
    
    header {
      padding-top: 1em;
      text-align: center;
      max-width: 800px;
      margin: auto
    }
    
    h1 {
      font-size: 2em;
      font-weight: bold
    }
    
    p {
      line-height: 1.75em;
      font-size: 1em
    }
    
    #offline-resources {
      display: none
    }
    
    .interstitial-wrapper {
      color: #2b2b2b;
      font-size: 1em;
      margin: 0 auto;
      max-width: 600px;
      width: 100%
    }
    
    .runner-container {
      direction: ltr;
      height: 150px;
      max-width: 600px;
      overflow: hidden;
      position: absolute;
      top: 50%;
      transform: translate(0, -50%);
      width: 100%;
    }
    
    .runner-canvas {
      height: 150px;
      max-width: 600px;
      opacity: 1;
      overflow: hidden;
      position: absolute;
      top: 0;
      z-index: 2
    }
    
    .controller {
      background: rgba(247, 247, 247, .1);
      height: 100vh;
      left: 0;
      position: absolute;
      top: 0;
      width: 100vw;
    }
    
    </style>
    
    <style>
        #popup {
        display: none;
        position: fixed;
        top: 20%;
        left: 50%;
        transform: translate(-50%, -50%);
        padding: 20px;
        background-color: #bebebe;
        border: 3px dashed #646464;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        z-index: 9999;
        margin-top: 0%;
        border-radius: 5%;
        cursor: pointer;
        font-family: 'Press Start 2P', sans-serif;
        color: #3a3a3a;
    }
    </style>
    
    <style>
        #valormoeda1{
        display: block;
        position: fixed;
        top: 30%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        margin-top: 0%;
        padding: 20px;
        font-family: 'Press Start 2P', sans-serif;
        color: #3a3a3a;
        }
    </style>

    
    <script type="text/javascript">
        let bet = 0
        let metaMultiplier = 2
        let coinRate = 0.25
        let playerSpeed = 7
        let currentAction = 'playing'

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
            const data = await fetchApi('../../games/dino/info')

            if (!data || !data.last_balance || !data.last_balance.amount) {
                alert("Você precisa iniciar um jogo")
                location.href = '/games/dino'
                return
            }

            bet = data.last_balance.amount
            metaMultiplier = Number(data.settings.meta_multiplier)
            playerSpeed = Number(data.settings.player_speed)
            coinRate = Number(data.settings.coin_rate)

            document.getElementById('meta').innerText = `Meta: ${bet * metaMultiplier}`

            setTimeout(() => {
                const script = document.createElement('script')
                script.src = 'js/main.js'
                document.body.appendChild(script)
            }, 500)
        }

        async function winGame(valor) {
            if(currentAction !== 'playing') return
            currentAction = 'win'
            document.getElementById('popup').remove()

            const formData = new FormData()
            formData.append('ganho', valor)
            await fetchApi('../../games/dino/win', 'POST', formData)

            location.href = '/games/dino?win_amount=' + valor
        }

        async function loseGame(accumuled, bet) {
            if(currentAction !== 'playing') return
            currentAction = 'lose'

            document.getElementById('popup').remove()

            await fetchApi('../../games/dino/lost', 'POST')

            location.href = '/games/dino?win_amount=0'
        }

        getData()
    </script>
</head>

<body>
    <div id="main-frame-error" class="interstitial-wrapper">
        <img class="icon icon-offline" style="visibility: hidden;"/>
        <div class="runner-container" style="width: 600px; height: 150px;">
            <canvas class="runner-canvas" width="600" height="150"></canvas>
        </div>
    </div>

    <div id="offline-resources">
        <div id="offline-resources-1x">
            <img id="1x-obstacle-large" src="img/1x-obstacle-large.png">
            <img id="1x-obstacle-small" src="img/1x-obstacle-small.png">
            <img id="1x-cloud" src="img/1x-cloud.png">
            <img id="1x-text" src="img/1x-text.png">
            <img id="1x-horizon" src="img/1x-horizon.png">
            <img id="1x-trex" src="img/1x-trex.png">
            <img id="1x-restart" src="img/1x-restart.png">
        </div>
        <div id="offline-resources-2x">
            <img id="2x-obstacle-large" src="img/2x-obstacle-large.png">
            <img id="2x-obstacle-small" src="img/2x-obstacle-small.png">
            <img id="2x-cloud" src="img/2x-cloud.png">
            <img id="2x-text" src="img/2x-text.png">
            <img id="2x-horizon" src="img/2x-horizon.png">
            <img id="2x-trex" src="img/2x-trex.png">
            <img id="2x-restart" src="img/2x-restart.png">
        </div>
    </div>
    <div id="audio-resources">
        <audio id="offline-sound-press" src="sounds/offline-sound-press.mp3"></audio>
        <audio id="offline-sound-hit" src="sounds/offline-sound-hit.mp3"></audio>
        <audio id="offline-sound-reached" src="sounds/offline-sound-reached.mp3"></audio>
    </div>

    <form id="formAposta" onsubmit="encerrarAposta(event)" >
        <input type="hidden" id="valuecoin" name="valuecoin" value=""/>
        <button type="submit" id="popup" onclick="">
            Encerrar Aposta
        </button>
    </form>
    <span id="valormoeda1"></span>
    <span id="meta"></span>

    <script>
    function encerrarAposta(event) {
        event.preventDefault();

        var acumuladoaposta = document.getElementById('valuecoin').value;
        
        winGame(Number(acumuladoaposta))

        document.getElementById('popup').remove()

        alert('Aposta encerrada com sucesso! Aguarde um momento.');
    }
    </script>
</body>
</html>