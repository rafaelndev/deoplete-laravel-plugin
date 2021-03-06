from .base import Base

import re
import json
import os

class Source(Base):
    def __init__(self, vim):
        Base.__init__(self, vim)

        self.debug_enabled = True
        self.name = 'laravel-plugin'
        self.mark = '[Route]'
        self.filetypes = ['php', 'blade']
        self.is_bytepos = True
        self.rank = 500
        self.input_pattern = r'\w*'

    def get_complete_position(self, context):
        m = re.search(r'\w*[.@.\\\w]*$', context['input'])
        return m.start() if m else -1

    def gather_candidates(self, context):
        # If the composer file is not found, ignore
        if os.path.isfile('composer.json'):
            with open('composer.json') as data_file:
                data = json.load(data_file)
                # If the project is not laravel, ignore
                if 'laravel/framework' not in data['require']:
                    return []
        else:
            return []

        if self.check_route(context['input']):
            return self.vim.call('laravel_plugin#getRoutes', context['input'], context['complete_str'])
        else:
            if self.check_view(context['input']):
                return self.vim.call('laravel_plugin#getViews', context['input'], context['complete_str'])
            else:
                return []

    def check_route(self, input):
        is_in_router = re.compile(r"(.|\s)*(?:Route::(get|post|delete|put|patch|options)\(|action\(|\'uses\').*?\'")
        if is_in_router.match(input):
            self.mark = '[Route]'
            return True
        else:
            return False

    def check_view(self, input):
        is_in_view = re.compile(r"(.|\s)*(?:view\(|@extends\(|@include\(|@each\().*?\'")
        if is_in_view.match(input):
            self.mark = '[View]'
            return True
        else:
            return False
