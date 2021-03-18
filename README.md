# **DEPRECATED**
I don't use deoplete/php/laravel anymore, so this plugin is deprecated and probabily don't work anymore.

# Deoplete Laravel Plugin

## Introduction

Autocompletion for Laravel Routes and Views in Vim, for neovim-deoplete.

## Features

- Autocomplete Laravel Routes based on controllers public methods.
    - Works on following commands:
      - `Route:get, post, delete, put, patch, options('...')`
      - Blade `action('...')`
      - Route `'uses' => '...'`
- Autocomplete Laravel Blade Views.
    - Works on following commands:
      - `view('...')`
      - `@extends('...')`
      - `@include('...')`
      - `@each('...')`
- Cache for faster completion (Alpha).

## Installation and usage

### Requeriments
- PHP 5.4+
- Composer
- Python 2/3
- Neovim / Deoplete Plugin
- A Laravel Project (tested on 5.1+)

### Installation

You can use your plugin manager of choice, but we recommend [Vim-Plug](https://github.com/junegunn/vim-plug/)
- [vim-plug](https://github.com/junegunn/vim-plug)
  - Add `Plug 'rafaelndev/deoplete-laravel-plugin', {'for': ['php'], 'do': 'composer install'}` to .vimrc
  - Run `:PlugInstall`

### Usage
If your system meet all requirements, it should works automatically when typing the supported functions (see Features).

## TODO
- [ ] Section completion.
- [ ] Support for common vim (omnifunc).
- [ ] PHP Server to receive and send info to vim/neovim (possibly using ReactPHP HTTP or Sockets).
- [ ] Better cache mechanics.
