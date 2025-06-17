<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rick and Morty Characters</title>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .character-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
        }
        .modal-image {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div id="app" class="container mt-4">
        <h1 class="mb-4">Personajes de Rick and Morty</h1>
        
        <div class="mb-3">
            <button @click="fetchCharacters" class="btn btn-primary me-2">Cargar Personajes</button>
            <button @click="storeCharacters" class="btn btn-success me-2" :disabled="!characters.length">Guardar en Base de Datos</button>
            <button @click="loadStoredCharacters" class="btn btn-info">Ver Personajes Guardados</button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th>Especie</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="character in characters" :key="character.id">
                        <td>@{{ character.id }}</td>
                        <td><img :src="character.image" :alt="character.name" class="character-image"></td>
                        <td>@{{ character.name }}</td>
                        <td>@{{ character.status }}</td>
                        <td>@{{ character.species }}</td>
                        <td>
                            <button @click="showDetails(character)" class="btn btn-info btn-sm me-2">Detalle</button>
                            <button v-if="isStoredView" @click="editCharacter(character)" class="btn btn-warning btn-sm">Editar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal de Detalles -->
        <div class="modal fade" id="detailsModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalles del Personaje</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="selectedCharacter" class="text-center">
                            <img :src="selectedCharacter.image" :alt="selectedCharacter.name" class="modal-image mb-3">
                            <h4>@{{ selectedCharacter.name }}</h4>
                            <p><strong>Tipo:</strong> @{{ selectedCharacter.type || 'N/A' }}</p>
                            <p><strong>Género:</strong> @{{ selectedCharacter.gender }}</p>
                            <p><strong>Origen:</strong> @{{ selectedCharacter.origin?.name }}</p>
                            <p><strong>URL Origen:</strong> <a :href="selectedCharacter.origin?.url" target="_blank">@{{ selectedCharacter.origin?.url }}</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Edición -->
        <div class="modal fade" id="editModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Personaje</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="editingCharacter">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" v-model="editingCharacter.name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <input type="text" class="form-control" v-model="editingCharacter.status">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Especie</label>
                                <input type="text" class="form-control" v-model="editingCharacter.species">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" @click="updateCharacter">Guardar Cambios</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        new Vue({
            el: '#app',
            data: {
                characters: [],
                selectedCharacter: null,
                editingCharacter: null,
                isStoredView: false,
                detailsModal: null,
                editModal: null
            },
            mounted() {
                this.detailsModal = new bootstrap.Modal(document.getElementById('detailsModal'));
                this.editModal = new bootstrap.Modal(document.getElementById('editModal'));
            },
            methods: {
                async fetchCharacters() {
                    try {
                        const response = await axios.get('/characters/fetch');
                        this.characters = response.data;
                        this.isStoredView = false;
                    } catch (error) {
                        console.error('Error al cargar personajes:', error);
                        alert('Error al cargar los personajes');
                    }
                },
                async storeCharacters() {
                    try {
                        await axios.post('/characters/store');
                        alert('Personajes guardados exitosamente');
                    } catch (error) {
                        console.error('Error al guardar personajes:', error);
                        alert('Error al guardar los personajes');
                    }
                },
                async loadStoredCharacters() {
                    try {
                        const response = await axios.get('/characters/stored');
                        this.characters = response.data;
                        this.isStoredView = true;
                    } catch (error) {
                        console.error('Error al cargar personajes guardados:', error);
                        alert('Error al cargar los personajes guardados');
                    }
                },
                showDetails(character) {
                    this.selectedCharacter = character;
                    this.detailsModal.show();
                },
                editCharacter(character) {
                    this.editingCharacter = { ...character };
                    this.editModal.show();
                },
                async updateCharacter() {
                    try {
                        await axios.put(`/characters/${this.editingCharacter.id}`, this.editingCharacter);
                        const index = this.characters.findIndex(c => c.id === this.editingCharacter.id);
                        this.characters[index] = { ...this.editingCharacter };
                        this.editModal.hide();
                        alert('Personaje actualizado exitosamente');
                    } catch (error) {
                        console.error('Error al actualizar personaje:', error);
                        alert('Error al actualizar el personaje');
                    }
                }
            }
        });
    </script>
</body>
</html> 