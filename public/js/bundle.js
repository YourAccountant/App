require('../scss/bundle.scss');

$.put = function(url, data, callback, type){

  if ( $.isFunction(data) ){
    type = type || callback,
    callback = data,
    data = {}
  }

  return $.ajax({
    url: url,
    type: 'PUT',
    success: callback,
    data: data,
    contentType: type
  });
}

$.delete = function(url, data, callback, type){

  if ( $.isFunction(data) ){
    type = type || callback,
        callback = data,
        data = {}
  }

  return $.ajax({
    url: url,
    type: 'DELETE',
    success: callback,
    data: data,
    contentType: type
  });
}

$('#signin, #signup, #create-partner').submit(function(e) {
    e.preventDefault();

    let json = {}
    $(this).serializeArray().forEach(d => {
        json[d.name] = d.value;
    })

    $.post($(this).attr('action'), JSON.stringify(json))
});

$('#get-me').click(_ => {
    $.get('/api/v1/client/me')
})

$('#get-administrations').click(_ => {
    $.get('/api/v1/administration')
})

$("#create-administration").submit(function(e) {
    e.preventDefault()

    let json = {}
    $(this).serializeArray().forEach(d => {
        json[d.name] = d.value;
    })

    $.post($(this).attr('action'), JSON.stringify(json))
})

$('#update-administration').submit(function (e) {
    e.preventDefault()

    let json = {}
    $(this).serializeArray().forEach(d => {
        json[d.name] = d.value;
    })

    $.put($(this).attr('action') + '/' + json.id, JSON.stringify(json))
})

$('#delete-administration').submit(function (e) {
    e.preventDefault()

    let json = {}
    $(this).serializeArray().forEach(d => {
        json[d.name] = d.value;
    })

    $.delete($(this).attr('action') + '/' + json.id)
})
