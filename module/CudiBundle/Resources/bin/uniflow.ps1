$clientId = $args[0]
$clientSecret = $args[1]
$universityMail = $args[2]
$amount = $args[3]
Write-Host $clientId

#if (Get-Module -ListAvailable -Name NTware.Ufo.PowerShell.ObjectManagement) {
#    Write-Host "Module already installed"
#}
#else {
#    Write-Output Y | Install-Module -Name NTware.Ufo.PowerShell.ObjectManagement
#}

#Import-Module NTware.Ufo.PowerShell.ObjectManagement
Import-Module .\NTware.Ufo.PowerShell.ObjectManagement.dll
$secStringPassword = ConvertTo-SecureString $clientSecret -AsPlainText -Force

$credObject = New-Object System.Management.Automation.PSCredential($clientId, $secStringPassword)

try {
    Open-MomoConnection -TenantDomain 'vtk.eu.uniflowonline.com' -NonInteractiveUserApplication $credObject

    Write-Host "test"

    Add-MomoUserBudget -Email $universityMail -Amount $amount -Wallet Secondary
} catch {
    "An error Occurred!"
}

