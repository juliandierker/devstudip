# makefile for vips/locale

# list of message catalogs to create
CATALOGS = locale/en/LC_MESSAGES/vips.mo

# list of translatable source files
SOURCES = $(wildcard *.php controllers/*.php exercises/*.php lib/*.php views/*/*.php)

# default rules for gettext handling
%.pot: $(SOURCES)
	xgettext -k_vips -kn_vips:1,2 -o $@ --from-code=ISO-8859-1 $(SOURCES)
	msgconv -t ISO-8859-1 -o $@ $@

%.po: %.pot
	msgmerge -qU $@ $<

%.mo: %.po
	msgfmt -o $@ $<

# default target: create all message catalogs
all: $(CATALOGS)
