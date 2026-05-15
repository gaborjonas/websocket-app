.PHONY: setup-web start-server setup-web setup-server setup

start:
	$(MAKE) start-web & $(MAKE) start-server

start-server:
	composer -d server run:server

start-web:
	npm --prefix web run dev

setup-web:
	npm --prefix web install

setup-server:
	composer -d server install

setup: setup-web setup-server