{
  description = "Laravel dev shell (php84)";

  # Per-project nixpkgs pin. `nix flake update` bumps THIS project only.
  inputs.nixpkgs.url = "github:NixOS/nixpkgs/nixos-unstable";

  outputs =
    { self, nixpkgs }:
    let
      system = "x86_64-linux";
      pkgs = nixpkgs.legacyPackages.${system};

      # ── The two per-project knobs ────────────────────────────────────────
      # 1) PHP version: pkgs.php84 / pkgs.php85
      # 2) Extensions: `enabled` is nixpkgs' default, Laravel-ready set
      #    (mbstring, openssl, tokenizer, curl, xml, pdo_sqlite, …). Uncomment
      #    to add project extras; composer below is built against this php.
      php = pkgs.php84.buildEnv {
        extensions =
          { enabled, all }:
          enabled
          ++ (with all; [
            # gd          # image processing
            # intl        # i18n, number/date formatting
            # imagick     # ImageMagick bindings
            # xdebug      # step debugging / coverage (dev only)
          ]);
        extraConfig = ''
          memory_limit = 512M
          upload_max_filesize = 64M
          post_max_size = 64M
        '';
      };
      # ─────────────────────────────────────────────────────────────────────
    in
    {
      devShells.${system}.default = pkgs.mkShell {
        packages = [
          php
          php.packages.composer # Composer built against THIS php — no version skew
          pkgs.nodejs_24 # Vite / frontend build
        ];

        shellHook = ''
          projectRoot=$(${pkgs.git}/bin/git rev-parse --show-toplevel 2>/dev/null || printf '%s' "$PWD")
          export PATH="$projectRoot/vendor/bin:$PATH"

          echo "Laravel dev shell: php $(php -r 'echo PHP_VERSION;'), node $(node -v)" >&2
        '';
      };
    };
}
