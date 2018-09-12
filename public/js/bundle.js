require('../scss/bundle.scss');

$('#signin, #signup').on('submit', function(e) {
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

function getCookie(name) {
  var value = "; " + document.cookie;
  var parts = value.split("; " + name + "=");
  if (parts.length == 2) return parts.pop().split(";").shift();
}

$('#get').on('click', function (e) {
    $.get('/api/v1/client/me', d => {
        getCookie("authorization");
    })
})
