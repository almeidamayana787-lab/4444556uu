let meta = 0;
let xmeta = 2;
let bet = 1;
let gameSpeed = 820;
let velocidade_game = 820;
let fruitRate = 1;

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
    const data = await fetchApi('../../games/fruit/info')

    if (!data || !data.last_balance || !data.last_balance.amount) {
        alert("Você precisa iniciar um jogo")
        location.href = '/games/fruit'
        return
    }

    bet = Number(data.last_balance.amount)
    xmeta = Number(data.settings.meta_multiplier)
    gameSpeed = Number(data.settings.drop_duration)
    fruitRate = Number(data.settings.fruit_rate)

    meta = bet * xmeta;
    velocidade_game = gameSpeed;

    const script = document.createElement('script');
    script.src = 'scripts/all.js';

    document.head.appendChild(script);
}

getData()

async function winGame(valor) {
    const formData = new FormData()
    formData.append('ganho', valor)
    await fetchApi('../../games/fruit/win', 'POST', formData)

    location.href = '/games/fruit?win_amount=' + valor
}

async function loseGame(accumuled, bet) {
    await fetchApi('../../games/fruit/lost', 'POST')

    location.href = '/games/fruit?win_amount=0'
}