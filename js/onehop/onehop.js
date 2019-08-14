function getTemplate(){
    var templateid = document.getElementById("smstemplate").value;
    var currentLocation = window.location;
    document.getElementById("sms_text").value = '';
    if(templateid > 0) {
        document.getElementById("sms_text").value = 'Loading template body...';
        xmlhttp=new XMLHttpRequest();
        xmlhttp.onreadystatechange=function()
        {
            if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
                //document.getElementById("sms_text").innerHTML=xmlhttp.responseText;
                var templatebody = JSON.parse(xmlhttp.responseText);
                document.getElementById("sms_text").value=templatebody['temp_body'];
            }
        }
        xmlhttp.open("GET",currentLocation+"templateid/"+templateid,true);
        xmlhttp.send();
    }
}