VERSION = "1.1-Beta"
VERSION2 = $(shell echo $(VERSION)|sed 's/ /-/g')
ZIPFILE = lib_attachments_remapper_$(VERSION2).zip

FILES = *.xml *.php index.html README.md

all: $(ZIPFILE)

ZIPIGNORES = -x "*.zip" -x "*~" -x "*.git/*" -x "*.gitignore"

$(ZIPFILE): $(FILES)
	@echo "-------------------------------------------------------"
	@echo "Creating extension zip file: $(ZIPFILE)"
	@echo ""
	@zip -r ../$@ $(FILES) $(ZIPIGNORES)
	@mv ../$@ .
	@echo "-------------------------------------------------------"
	@echo "done"
