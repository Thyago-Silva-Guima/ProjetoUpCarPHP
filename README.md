# 🚗 UPCAR - Sistema de Caronas Universitárias

![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

O **UPCAR** é uma aplicação web desenvolvida em PHP puro (arquitetura MVC) para facilitar a partilha de viagens (caronas) entre estudantes universitários. O sistema conecta motoristas com lugares disponíveis nos seus veículos a passageiros que partilham o mesmo trajeto, promovendo a mobilidade, economia e o networking acadêmico.

---

##  Funcionalidades

O sistema está dividido em dois perfis principais:

###  Perfil: Passageiro
* **Pesquisa e Solicitação:** Procurar e solicitar lugares em viagens disponíveis.
* **Gestão de Reservas:** Visualizar o estado das vagas solicitadas (Aguardando, Aceita, Recusado) e cancelar reservas pendentes.
* **Histórico de Parceiros:** Ver o histórico de motoristas com quem já viajou.
* **Email e senha de contas da Pré carga:** email:passageiro1@upcar.com / passageiro2@upcar.com / passageiro3@upcar.com / passageiro4@upcar.com senha: 123456 .

###  Perfil: Motorista
* **Publicação de Viagens:** Criar, editar e excluir rotas de caronas indicando origem, destino, data/hora e lugares disponíveis.
* **Gestão de Pedidos:** Aceitar ou recusar solicitações de lugares feitas por passageiros.
* **Painel de Controle:** Dashboard intuitivo para visualizar o número de reservas solicitadas, aceitas e recusadas.
* **Email e senha de contas da Pré carga:** email:motorista1@upcar.com / motorista2@upcar.com / motorista3@upcar.com / motorista4@upcar.com senha: 123456 .

### Segurança e Autenticação
* Registo de novos utilizadores com validação de dados.
* Login encriptado com proteção CSRF em todos os formulários.

---

##  Tecnologias Utilizadas

* **Backend:** PHP
* **Frontend:** HTML5, Tailwind CSS
* **Base de Dados:** MySQL
* **Servidor Local:** Apache (via XAMPP / LAMPP)

---

##  Como executar o projeto localmente

## Para usar a preCargaBanco.php
localhost/ProjetoUpCarPHP-main/popular_banco.php

### 1. Pré-requisitos
Certifique-se que o **XAMPP** está instalado na sua máquina.

### 2. Clonar o Repositório
Navega até à pasta pública do teu servidor (ex: `htdocs` no XAMPP) e clona o projeto:
```bash
git clone https://github.com/Thyago-Silva-Guima/ProjetoUpCarPHP.git

```


 <br>
 <hr>

<p align="center"> Developed by:</b> Thyago Silva, <i>Murilo Opis </i>, <i>Luan Neuwirth</i>, <i>Lorenzo Vanelli</i></p>

> <sub align="center">**UPCAR © 2026** · Desenvolvido para fins acadêmicos.</sub> 
