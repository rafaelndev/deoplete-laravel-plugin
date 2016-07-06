from .base import Base

import re

class Source(Base):
    def __init__(self, vim):
        Base.__init__(self, vim)

        self.debug_enabled = True
        self.name = 'laravel-plugin'
        self.mark = '[Route]'
        self.filetypes = ['php']
        self.is_bytepos = True
        self.rank = 500
        self.input_pattern = r'\w*'
        # self.input_pattern= '#\w+'

    def get_complete_position(self, context):
        self.debug('TESTEEEEEEEEEEEE')
        m = re.search(r'\w*[.@\w]*$', context['input'])
        return m.start() if m else -1

    def gather_candidates(self, context):
        check_route = re.compile(r"(.|\s)*(?:Route::(get|post|delete|put|patch|options)\(|action\(|\'uses\').*?\'")
        if check_route.match(context['input']):
            return self.vim.call('laravel_plugin#getRoutes', context['input'], context['complete_str'])
        else:
            check_view = re.compile(r"(.|\s)*(?:view\(|@extends\(|@include\(|@each\().*?\'")
            if check_view.match(context['input']):
                return self.vim.call('laravel_plugin#getViews', context['input'], context['complete_str'])
            else:
                return []
