var datetostring = {};
datetostring.datetimetoindonesia = function (data)
{
    var date    = new Date(data);
    var year    = date.getFullYear();
    var month   = date.getMonth();
    var tanggal = date.getDate();
    var day     = date.getDay();
    var hours   = date.getHours();
    if (hours < 10) 
    {
        hours = "0" + hours;
    }
    var min     = date.getMinutes();
    if (min < 10) 
    {
        min = "0" + min;
    }
    var arrayMonth = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    var dayList    = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    var monthname  = arrayMonth[month];
    var dayname    = dayList[day];

    return dayname+', '+tanggal+' '+monthname+' '+year+' '+hours+':'+min;
}

datetostring.datetoindonesia = function (data)
{
    var date    = new Date(data);
    var year    = date.getFullYear();
    var month   = date.getMonth();
    var tanggal = date.getDate();
    var day     = date.getDay();
    
    var arrayMonth = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    var dayList    = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    var monthname  = arrayMonth[month];
    var dayname    = dayList[day];

    return dayname+', '+tanggal+' '+monthname+' '+year;
}

datetostring.secondtominutes = function (milliseconds) 
{
    var minutes = Math.floor(milliseconds / 60);
    var seconds = ((milliseconds % 60)).toFixed(0);
    return minutes + ":" + (seconds < 10 ? '0' : '') + seconds;
}

// Later This Function Will Deleted If All Source Depends Of This Function Has Refactoring
// Use datestring.datetimetoindonesia instead
function datetostringindonesia(data)
{
    var date    = new Date(data);
    var year    = date.getFullYear();
    var month   = date.getMonth();
    var tanggal = date.getDate();
    var day     = date.getDay();
    var hours   = date.getHours();
    if (hours < 10) 
    {
        hours = "0" + hours;
    }
    var min     = date.getMinutes();
    if (min < 10) 
    {
        min = "0" + min;
    }
    var arrayMonth = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    var dayList    = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    var monthname  = arrayMonth[month];
    var dayname    = dayList[day];

    return dayname+', '+tanggal+' '+monthname+' '+year+' '+hours+':'+min;
}

datetostring.minutestohourstring = function (minutes) {
    var sec_num = minutes * 60; // don't forget the second param
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    return hours+':'+minutes+':'+seconds;
}