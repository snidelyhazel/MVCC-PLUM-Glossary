<!doctype html>
<html>
  <head>
    <meta charset="UTF-8"/>
    <style>
      html
      {
        font-family: sans-serif;
        font-size: 16px;
        line-height: 1.4;
        margin: 0 20px;
      }

      body
      {
        max-width: 70ch;
        margin: auto;
      }

      .term
      {
        margin-right: 20px;
      }

      .definition
      {

      }
    </style>
  </head>
  <body>
    <script>
      // Include glossary raw text file.
      let glossaryraw = `<?php include("includes/glossaryraw.txt");?>`;

      // Split glossary entries by newline character into an array.
      let glossaryarray = glossaryraw.split("\n");

      // Make empty array to hold formatted entries, initialize empty string to
      // hold partial entries, initialize character for alphabetical comparison.
      let formattedEntries = [];
      let tempEntry = "";
      let compchar = "A";

      function removeSpecialCharacters(text)
      {
        // This regular expression matches any number of consecutive special
        // characters (not a letter or number, ignoring case). All groups of
        // special characters will be replaced with an underscore.
        return text.replace(/[^A-Z0-9]+/ig, "_").toLowerCase();
      }

      // Iterate over glossaryarray.
      for (let i = 0; i < glossaryarray.length; i++)
      {
        // Each element in the glossaryarray is a glossaryline, that is, a
        // glossary entry in whole or part.
        // Initialize a string variable to hold a line of text that represents
        // a partial entry, as well as a string variable to hold the first
        // character of that line for detection of a new glossary entry.
        let glossaryline = glossaryarray[i];
        let firstchar = glossaryline[0];

        // If a line is empty, it doesn't contain any character let alone a
        // first character, so ignore and continue.
        if (firstchar === undefined)
        {
          continue;
        }
        // If the first character is uppercase and it appears to be in
        // alphabetical order, assume a new glossary entry.
        // See comment within conditional for example of missing letters.
        if (firstchar === firstchar.toUpperCase() && (firstchar === compchar || firstchar === String.fromCharCode(compchar.charCodeAt(0) + 1) || (firstchar === compchar || firstchar === String.fromCharCode(compchar.charCodeAt(0) + 1) /*|| (firstchar === "L" && compchar === "J")*/)))
        {
          // Once the start of a new entry is determined, update what the first
          // character of the current line is for future comparison, insert the
          // preceding entry into the array of formatted definitions, initialize
          // a new entry with the contents of the current line.
          compchar = firstchar;
          formattedEntries.push(tempEntry);
          tempEntry = glossaryline;
        }
        // If not the start of a new entry.
        else
        {
          // Add contents of current line to current entry.
          tempEntry += " " + glossaryline;
        }
      }

      // Push whatever entry is left in the tempEntry string into the array of
      // formatted entries.
      formattedEntries.push(tempEntry);

      // Turn raw glossary data into JSON file.
      function jsonize()
      {
        // Initialize an array to contain structured JSON data.
        let jsonarray = [];

        // Iterate over array of formatted entries.
        for (let i = 0; i < formattedEntries.length; i++)
        {
          // For each entry, find index of first period, denoting the divider
          // between term and definition.
          let entry = formattedEntries[i];
          let periodindex = entry.indexOf(".");

          // Divide entry into term and definition.
          let term = entry.slice(0, periodindex);
          let definition = entry.slice(periodindex + 2);

          // Store in array structured as JSON data.
          jsonarray.push({term: term, definition: definition});
        }

        // Write JSON data to screen in preformatted fixed-width text.
        document.writeln("<pre>" + JSON.stringify(jsonarray, null, '\t') + "</pre>");
      }

      function textify()
      {
        // Iterate over array of formatted entries.
        for (let i = 0; i < formattedEntries.length; i++)
        {
          // For each entry, find index of first period, denoting the divider
          // between term and definition.
          let entry = formattedEntries[i];
          let periodindex = entry.indexOf(".");

          // Divide entry into term and definition.
          let term = entry.slice(0, periodindex);
          let definition = entry.slice(periodindex + 2);

          // Write data to screen with line breaks.
          document.writeln(entry + "<br/>");
        }
      }
    </script>
  </body>
</html>
