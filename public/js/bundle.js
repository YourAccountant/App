require('../scss/bundle.scss');

$('#signin, #signup, #create-partner').on('submit', function(e) {
    e.preventDefault();

    let data = $(this).serializeArray()

    let json = {}
    data.forEach(d => {
        json[d.name] = d.value;
    })

    let url = $(this).attr('action');

    $.post(url, JSON.stringify(json), d => {
        console.log('success', d);
    })
    .done(d => {
        console.log('done', d);
    })
    .fail(d => {
        console.log('success', d);
    })
});

$('#get').on('click', function (e) {
    $.get('/api/v1/client/me', d => {
        console.log(d);
    })
})
