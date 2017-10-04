/* Replace with your SQL commands */
ALTER TABLE project
  ADD COLUMN coinmarketcap_id text UNIQUE;


CREATE TABLE latest_coinmarketcap_data
(
  id text,
  name text,
  symbol text,
  price_usd numeric,
  market_cap_usd numeric,
  update_time timestamp NOT NULL,
  PRIMARY KEY (id)
)


