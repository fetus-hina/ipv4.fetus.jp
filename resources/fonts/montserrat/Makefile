DST_WEBFONTS := dist/webfont/montserrat-bold.woff2 dist/webfont/montserrat-regular.woff2
DST_TTFS := $(addprefix dist/ttf/,$(notdir $(DST_WEBFONTS:.woff2=.ttf)))
SRC_FONTS := $(addprefix src/,Montserrat-Medium.ttf Montserrat-Bold.ttf)
DST_CSS := dist/webfont/montserrat.css dist/webfont/montserrat.min.css

.PHONY: all
all: $(DST_WEBFONTS) $(DST_CSS) $(SRC_FONTS)

.PHONY: clean
clean:
	rm -f $(DST_WEBFONTS) $(DST_TTFS) $(DST_CSS) $(SRC_FONTS)

.PHONY: dist-clean
dist-clean: clean

src/%.ttf:
	curl -o $@ -fsSL https://github.com/JulietaUla/Montserrat/raw/v7.222/fonts/ttf/$(notdir $@)

dist/webfont/%.woff2: dist/ttf/%.ttf
	npx ttf2woff2 < $< > $@

dist/ttf/montserrat-bold.ttf: src/Montserrat-Bold.ttf convert.ff
	fontforge -quiet -lang=ff -script ./convert.ff $< $@

dist/ttf/montserrat-regular.ttf: src/Montserrat-Medium.ttf convert.ff
	fontforge -quiet -lang=ff -script ./convert.ff $< $@

%.min.css: %.css
	npx postcss -u autoprefixer -u cssnano --no-map -o $@ $<

dist/webfont/montserrat.css: src/webfont.scss
	npx sass --charset --style=expanded --no-source-map $< $@
