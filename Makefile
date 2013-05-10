VERSION = "1.0"
VERSION2 = $(shell echo $(VERSION)|sed 's/ /-/g')
# ZIPFILE = comp_permtest_$(VERSION2).zip
ZIPFILE = lib_attachments_remapper.zip

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
