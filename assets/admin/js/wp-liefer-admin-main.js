// Replacement texts
const replacements = new Map([
  ["Add Entry", "Eintrag hinzufügen"],
  ["Collapse All", "Alle Schließen"],
  ["Expand All", "Alle Öffnen"],
  ["Food Types", "Lebensmittel Typ"],
  ["Food Type", "Lebensmittel Typ"],
  ["Lebensmitteltyp", "Lebensmittel Typ"],
  ["Mildness Foodtype", "Lebensmittel Typ"],
  ["Others Foodtype", "Lebensmittel Typ"],
  ["Additives", "Zusatzstoffe"],
  ["Select Time", "Uhrzeit"],
  ["No options", "Keine Optionen"],
  ["Select / Deselect All", "Alle auswählen / abwählen"],
]);

function wpLieferFindAndReplaceText(node, replacements) {
  if (node.nodeType === Node.TEXT_NODE) {
    let text = node.textContent;
    for (let [searchText, replacementText] of replacements.entries()) {
      text = text.replace(searchText, replacementText);
    }
    node.textContent = text;
  } else if (node.nodeType === Node.ELEMENT_NODE) {
    for (let child of node.childNodes) {
      wpLieferFindAndReplaceText(child, replacements);
    }
  }
}

const elements = document.getElementsByTagName("*");
for (let el of elements) {
  setTimeout(() => {
    wpLieferFindAndReplaceText(el, replacements);
  }, 100);
}

document.body.addEventListener("click", function () {
  for (let el of elements) {
    wpLieferFindAndReplaceText(el, replacements);
  }
});
