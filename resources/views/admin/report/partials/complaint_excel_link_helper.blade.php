<script>
    let complaintExcelLinks = {};
    const complaintFileColumnIndexes =
        @json($fileColumnIndexes ?? [($fileColumnIndex ?? 9)]);

    function complaintReportExcelBody(data, row, column, node) {
        if (!complaintFileColumnIndexes.includes(column)) {
            return data;
        }

        if (row === 0 && column === complaintFileColumnIndexes[0]) {
            complaintExcelLinks = {};
        }

        complaintExcelLinks[column] = complaintExcelLinks[column] || [];
        const link = $(node).find('a').first();

        if (!link.length) {
            return 'N/A';
        }

        const url = String(link.attr('href') || '');
        const friendlyName = String(link.text() || 'Complaint File').trim();

        complaintExcelLinks[column].push({
            url: url,
            friendlyName: friendlyName
        });

        return friendlyName;
    }

    function customizeComplaintExcelLinks(xlsx) {
        const sheet = xlsx.xl.worksheets['sheet1.xml'];
        const styles = xlsx.xl['styles.xml'];
        const spreadsheetNamespace =
            'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
        const fonts = styles.getElementsByTagNameNS(
            spreadsheetNamespace,
            'fonts'
        )[0];
        const cellFormats = styles.getElementsByTagNameNS(
            spreadsheetNamespace,
            'cellXfs'
        )[0];
        const hyperlinkFontId = fonts.children.length;
        const hyperlinkStyleId = cellFormats.children.length;
        const hyperlinkFont = styles.createElementNS(
            spreadsheetNamespace,
            'font'
        );

        [
            ['name', 'val', 'Calibri'],
            ['sz', 'val', '11'],
            ['color', 'rgb', 'FF0563C1'],
            ['u', null, null]
        ].forEach(function (definition) {
            const element = styles.createElementNS(
                spreadsheetNamespace,
                definition[0]
            );

            if (definition[1]) {
                element.setAttribute(definition[1], definition[2]);
            }

            hyperlinkFont.appendChild(element);
        });

        fonts.appendChild(hyperlinkFont);
        fonts.setAttribute('count', fonts.children.length);

        const hyperlinkFormat = styles.createElementNS(
            spreadsheetNamespace,
            'xf'
        );
        hyperlinkFormat.setAttribute('numFmtId', '0');
        hyperlinkFormat.setAttribute('fontId', hyperlinkFontId);
        hyperlinkFormat.setAttribute('fillId', '0');
        hyperlinkFormat.setAttribute('borderId', '0');
        hyperlinkFormat.setAttribute('xfId', '0');
        hyperlinkFormat.setAttribute('applyFont', '1');
        cellFormats.appendChild(hyperlinkFormat);
        cellFormats.setAttribute('count', cellFormats.children.length);

        complaintFileColumnIndexes.forEach(function (fileColumnIndex) {
            let fileColumnNumber = fileColumnIndex + 1;
            let fileColumnLetter = '';

            while (fileColumnNumber > 0) {
                fileColumnNumber--;
                fileColumnLetter =
                    String.fromCharCode(65 + (fileColumnNumber % 26)) +
                    fileColumnLetter;
                fileColumnNumber = Math.floor(fileColumnNumber / 26);
            }

            $('c[r^="' + fileColumnLetter + '"]', sheet).each(function () {
            const cell = this;
            const cellText = $('is t', cell).text() || $('v', cell).text();

            if (!/^Complaint File/.test(cellText)) {
                return;
            }

            const exportedLink = (complaintExcelLinks[fileColumnIndex] || []).shift();

            if (!exportedLink) {
                return;
            }

            const url = exportedLink.url;
            const friendlyName = exportedLink.friendlyName;
            const escapedUrl = url.replace(/"/g, '""');
            const escapedFriendlyName = friendlyName.replace(/"/g, '""');

            while (cell.firstChild) {
                cell.removeChild(cell.firstChild);
            }

            cell.setAttribute('t', 'str');
            cell.setAttribute('s', hyperlinkStyleId);

            const formula = sheet.createElementNS(
                spreadsheetNamespace,
                'f'
            );
            formula.textContent =
                'HYPERLINK("' + escapedUrl + '","' +
                escapedFriendlyName + '")';

            const cachedValue = sheet.createElementNS(
                spreadsheetNamespace,
                'v'
            );
            cachedValue.textContent = friendlyName;

            cell.appendChild(formula);
            cell.appendChild(cachedValue);
            });
        });
    }
</script>
