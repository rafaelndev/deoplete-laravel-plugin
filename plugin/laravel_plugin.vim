let s:plugindir = expand('<sfile>:p:h:h')
" --------------------------------
"  Function(s)
" --------------------------------
function! laravel_plugin#getRoutes(findstart, base) abort
    let l:route_list = []
    " if empty(a:base)
    "   return []
    " endif
    let l:routes = system("php " . s:plugindir . "/laravel/main.php" . " " . getcwd() . " " . shellescape(a:base))
    let l:route_split = split(l:routes,'\n')

    for l:route in l:route_split
      call add(l:route_list, l:route)
    endfor
    return l:route_list
endfunction

function! laravel_plugin#getViews(findstart, base) abort
  let l:views_list = globpath('resources/views', '**/*.blade.php')
  let l:views_list_filtered = []

  for l:item in split(l:views_list, '\n')
    let l:item_filtered = substitute(l:item, 'resources/views/', '', '')
    let l:item_filtered = substitute(l:item_filtered, '.blade.php', '', '')
    let l:item_filtered = substitute(l:item_filtered, '/', '.', 'g')

    if l:item_filtered =~ a:base
      call add(l:views_list_filtered, l:item_filtered)
    endif
  endfor

  return l:views_list_filtered
endfunction
