<!doctype html>
<html>
  <head>
    <meta charset="UTF-8"/>
    <title>Glossary of Planning and Land Use Terms</title>
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

      h1
      {
        font-size: 24px;
        text-align: center;
      }

      .alphaline
      {
        text-align: center;
      }

      .alphabet
      {

        font-size: 16px;
        font-weight: bold;
        text-decoration: none;
      }

      .icon
      {
        margin-right: 5px;
      }

      .arrow
      {
        font-size: 16px;
      }

      .search
      {
        margin: 20px;
        text-align: center;
        font-size: 18px;
      }

      .search input
      {
        font-size: inherit;
      }

      .highlight
      {
        background-color: rgba(166, 201, 238, 0.85);
      }

      .top
      {
        font-size: 12px;
        float: right;
        text-decoration: none;
      }

      .term
      {
        margin-right: 20px;
      }

      .definition
      {

      }

      .totd
      {
        border: 1px solid gray;
        padding: 10px;
        margin: 20px;
      }

      .totd h2
      {
        font-size: 18px;
        text-align: center;
      }

      .totd .term
      {
        display: block;
        font-size: 24px;
        text-align: center;
      }

      .totd .definition
      {
        display: block;
        text-align: justify;
        margin: 10px 20px 0 20px;
      }
    </style>
  </head>
  <body>
    <!--
    glossary of terms
    tagged plum or community plan or both
    search text field we can have toggleable labels to filter by category
    definitions searchable
    as you type in field, terms and definitions that do not contain search text are hidden
    a way to display by letter
    term of the day
    allow linking to specific terms with #fragmentidentifiers
    link see other terms, link codes
    search by individual words
    exact match top result
    -->

    <h1>Glossary of Planning and Land&nbsp;Use Terms</h1>


    <!--Search bar-->
    <div class="search"><span class="icon">🔍</span><input type="text" id="searchterm" placeholder="Search Glossary"/></div>

    <div id="dynamicglossary"></div>
    <script>

      // Initialize variables to represent search term and the glossary content.
      let searchterm = document.getElementById("searchterm");
      let dynamiccontent = document.getElementById("dynamicglossary");

      // Dynamic search.
      function updateSearch()
      {
        // If no search term use default glossary.
        if (searchterm.value.length === 0)
        {
          refresh();
        }
        else
        {
          contentHTML = "";

          for (let i = 0; i < glossary.length; i++)
          {
            // For each entry, access term and definition.
            let entry = glossary[i];

            // Attempt to find the search term in each entry or definition term.
            // If not found, entry does not match search filter; do not print.
            if (entry.term.toLowerCase().indexOf(searchterm.value.toLowerCase()) !== -1 || entry.definition.toLowerCase().indexOf(searchterm.value.toLowerCase()) !== -1)
            {
              contentHTML += printWithHighlightFromJSON(entry, searchterm.value);
            }
          }

          // Replace default glossary with glossary filtered by search term.
          dynamiccontent.innerHTML = contentHTML;
        }
      }

      // For every user input event, trigger updateSearch function.
      searchterm.addEventListener("input", updateSearch);


      // Include glossary JSON file.
      let glossary = <?php include("includes/glossary.json");?>;

      function printFromJSON(entry)
      {
        // Write entry to screen as term and definition.
        return ("<p id='" + removeSpecialCharacters(entry.term) + "'><b class='term'>" + entry.term + "</b> <span class='definition'>" + entry.definition + "</span></p>");
      }

      function printWithHighlightFromJSON(entry, searchterm)
      {
        return printFromJSON(
          {
            term: entry.term.split(searchterm).join("<span class='highlight'>" + searchterm + "</span>"),
            definition: entry.definition.split(searchterm).join("<span class='highlight'>" + searchterm + "</span>")
          });
      }

      function seededRandom(seed)
      {
        // Based on an algorithm by Hugo Elias.
        let n = (seed << 13 ^ seed) >>> 0;
        n = ((((((((((n * n) >>> 0) * 15731) >>> 0) + 789221) >>> 0) * n) >>> 0) + 1376312589) >>> 0);
        return n/* / 0xffffffff*/;
      }

      function removeSpecialCharacters(text)
      {
        // This regular expression matches any number of consecutive special
        // characters (not a letter or number, ignoring case). All groups of
        // special characters will be replaced with an underscore.
        return text.replace(/[^A-Z0-9]+/ig, "_").toLowerCase();
      }


      function refresh()
      {
        let contentHTML = "";

        // A | B | C | D | E | F | G | H | I | J | K | L | M | N | O | P | Q | R | S | T | U | V | W | X | Y | Z
        contentHTML += "<div class='alphaline'>";
        for (let i = 1; i <= 26; i++)
        {
          // Generate letters in alphabetical order and write to screen separated
          // by vertical bar character. Each letter is a link to the top of the
          // section of the glossary containing terms starting with that letter.
          let letter = String.fromCharCode("A".charCodeAt(0) - 1 + i);
          contentHTML += "<a class='alphabet' href='#" + letter + "'>" + letter + "</a>";
          if (i != 26)
          {
            contentHTML += " | ";
          }
        }
        contentHTML += "</div>";

        // Term of the Day
        // Create a Date object.
        let now = new Date();
        // The Date object contains the count of milliseconds elapsed since
        // January 1, 1970, which can be divided by the number of milliseconds
        // in one day and rounded down to nearest integer to determine the days.
        // Use the days since epoch to seed a pseudorandom number generator.
        let fullDaysSinceEpoch = Math.floor(now/8.64e7);
        // Pseudorandomly choose a number.
        let randomizer = seededRandom(fullDaysSinceEpoch);
        // Simple version: divide days by length and return remainder.
        let termOfTheDay = glossary[randomizer%glossary.length];

        contentHTML += "<div class='totd'><h2>📅 Term of the Day</h2>";
        contentHTML += printFromJSON(termOfTheDay);
        contentHTML += "</div>";


        // Sections
        // Initialize section letter.
        let glossarychar = "";

        // Iterate over array of formatted entries.
        for (let i = 0; i < glossary.length; i++)
        {
          // For each entry, access term and definition.
          let entry = glossary[i];
          let term = entry.term;
          let definition = entry.definition;

          // Compare first letter of current entry to current section letter.
          let letterCaps = term[0].toUpperCase();
          if (glossarychar !== letterCaps)
          {
            glossarychar = letterCaps;

            // If different, write section letter to top of new section.
            contentHTML += "<div><a class='top' href='#top'>Top <span class='arrow'>↑</span></a> <h3 class='alphaindex' id='" + glossarychar + "'>" + glossarychar + "</h3></div>";
          }

          contentHTML += printFromJSON(entry);
        }

        dynamiccontent.innerHTML = contentHTML;
      }

      refresh();
    </script>

  </body>
</html>
