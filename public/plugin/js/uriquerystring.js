var uriquerystring = {};
uriquerystring.replaceuriquerystring = function (querystring,newvalue)
{
    var queryParameters = {};
    var queryString = location.search.substring(1);
    var re = /([^&=]+)=([^&]*)/g, m;
    while (m = re.exec(queryString)) 
    {
        queryParameters[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
    }
    queryParameters[querystring] = newvalue;
    if(newvalue == '')
    {
        delete queryParameters[querystring];
    }
    location.search = $.param(queryParameters);  
}

uriquerystring.getallquerystringvariable = function()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

uriquerystring.updateQueryStringParameter = function(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
      return uri.replace(re, '$1' + key + "=" + value + '$2');
    }
    else {
      return uri + separator + key + "=" + value;
    }
  }