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
