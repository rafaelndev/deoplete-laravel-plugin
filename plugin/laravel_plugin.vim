let s:plugindir = expand('<sfile>:p:h:h')
" --------------------------------
"  Function(s)
" --------------------------------
function! laravel_plugin#getRoutes(findstart, base) abort
    let l:route_list = []
    let l:cached_routes = laravel_plugin#getCachedRoutes()
    if empty(l:cached_routes)
      let l:routes = system("php " . s:plugindir . "/laravel/main.php" . " " . getcwd() . " " . shellescape(a:base))
    else
      let l:routes = l:cached_routes
    endif

    let l:route_split = split(l:routes,'\n')

    for l:route in l:route_split
      call add(l:route_list, l:route)
    endfor
    return l:route_list
endfunction

function! laravel_plugin#getCachedRoutes() abort
python << endpython
import vim, json, os.path, time
from pprint import pprint
os.stat_float_times(False)

use_cache = True

with open('deoplete-laravel.cache') as data_file:
  data = json.load(data_file)

  classes = data[0]['classes']
  for file_class in classes:
    if file_class['date'] != os.path.getmtime(file_class['file']):
      use_cache = False

if use_cache:
  vim.command("let l:use_cache = 1")
  routes = ""
  for route in data[0]['routes']:
    routes += route
  vim.command("let l:routes = '%s'"% routes)
else:
  vim.command("let l:use_cache = 0")
endpython

if l:use_cache
  return l:routes
else
  return []
endif
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
