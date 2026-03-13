# git-auto.ps1

# Voeg alles toe
git add .

# Commit met datum en eventueel een message
$datum = Get-Date -Format "yyyyMMdd-HHmmss"
git commit -m "Automatische commit $datum"

# Push naar GitHub
git push origin main