install: package.json Gemfile
	npm install && bundle install

build: node_modules Gemfile.lock
	bundle exec ./node_modules/grunt-cli/bin/grunt build

dev: node_modules Gemfile.lock
	bundle exec ./node_modules/grunt-cli/bin/grunt dev

server: node_modules
	node ./node_modules/static-server/bin/static-server.js build

open: node_modules
	open http://localhost:9080

dev-server:
	make dev server open -j3

deploy:
	git push && cap deploy

node_modules: package.json
	npm install

Gemfile.lock: Gemfile
	bundle install

default: dev-server

.PHONY: install

.PHONY: build

.PHONY: dev

.PHONY: server

.PHONY: dev-server
