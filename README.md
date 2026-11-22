# SIGH-CE: Sistema de Gestão Hoteleira

Sistema web em **PHP** para a operação do dia a dia de um hotel: da chegada do hóspede ao check-out, incluindo os consumos durante a estadia e os relatórios da gerência.

## Funcionalidades

- **Login com perfis de acesso**: senhas com hash (`password_hash` / `password_verify`); algumas páginas só a **gerência** acessa
- **Pré-cadastro de hóspedes** e fila de pré-cadastros aguardando a recepção
- **Check-in e check-out**, com a lista de hóspedes ativos
- **Consumos durante a estadia**: restaurante, spa e lavanderia
- **Relatórios**: financeiro e de serviços utilizados
- **Cadastro de usuários** do sistema (feito pela gerência)

## Tecnologias

- PHP (sessões, formulários, `password_hash`)
- HTML e CSS
- Os dados ficam em arquivos **JSON** (sem banco de dados)

## Como rodar

1. Tenha o PHP 8+ instalado.
2. Clone o repositório e entre na pasta:
   ```bash
   git clone https://github.com/Henrique-Farini/hotelfinal.com.br.git
   cd hotelfinal.com.br
   ```
3. Crie a pasta `data/`, onde o sistema guarda os arquivos JSON.
4. Suba o servidor embutido do PHP e abra `http://localhost:8000`:
   ```bash
   php -S localhost:8000
   ```

## Estrutura

| Arquivo | Função |
|---|---|
| `index.php` | Login |
| `precadastro_form.php`, `lista_precadastros.php` | Pré-cadastro de hóspedes |
| `salvar_checkin.php`, `checkout.php`, `hospedes_ativos.php` | Check-in, check-out e hóspedes ativos |
| `restaurante.php`, `spa.php`, `lavanderia.php` | Consumos durante a estadia |
| `relatorio_financeiro.php`, `relatorio_servicos.php` | Relatórios da gerência |
| `cadastro_usuario.php` | Cadastro de usuários |

---

Projeto desenvolvido por [Henrique Farini](https://github.com/Henrique-Farini) no curso técnico em Desenvolvimento de Sistemas (SENAI).
