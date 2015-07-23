function foldl(fun, acc, list){
  for(var i = 0; i < list.length; i++){
    acc = fun(acc, list[i]);
  }
  return acc;
}

function map(fun, list){
  return foldl(function(acc, e){acc.push(fun(e)); return acc}, [], list);
}

function filter(fun, list){
  return foldl(function(acc, e) {if (fun(e)) acc.push(e); return acc},
            [], list);
}

function find(fun, list){
  for (var i = 0; i < list.length; i++){
    if (fun(list[i])){
      return list[i];
    }
  }
  return undefined;
}

function parseData(list){
  var ret = [];
  for (var i = 0; i < list.length; i++){
    ret.push({"location": eval("new google.maps.LatLng(" + list[i].latitude + "," + list[i].longitude + ")"), "weight":list[i].count});
  }
  return ret;
}
