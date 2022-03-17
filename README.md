#Metacampus Unite

Aquest projecte compta amb les següents modificacions amb condicions especials:

- A causa de la necessitat d'utilitzar l'algoritme AES-GCM, s'ha implementat uns commits de la llibreria xmlseclib i simplesamlphp en base a [aquest comentari](https://github.com/simplesamlphp/saml2/issues/179#issuecomment-687597903) d'un dels desenvolupadors de la llibreria simplesamlphp a github.
  - Amb aquest propòsit, també s'utilitza l'última versió sense release del plugin auth_saml2, disponible en [aquest repositori](https://github.com/simplesamlphp/saml2/issues/179#issuecomment-687597903).