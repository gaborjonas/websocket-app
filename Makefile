.PHONY: setup-front-end setup-back-end setup

start:
	start-front-end & start-back-end

start-back-end:
	composer -d back run-server

start-front-end:
	npm --prefix front run dev

setup-front-end:
	npm --prefix front install

setup-back-end:
	composer -d back install

setup: setup-front-end setup-back-end