<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Illustrators &bull; Journey Group</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="app.css" rel="stylesheet">
</head>
<body>
  <div id="app">
    <header>
      <h1>{{ title }}</h1>
      <div>A tool by <a href="http://journeygroup.com">Journey Group</a></div>
    </header>

    <form class="add-form" @submit.prevent="addIllustrator()">
      <div class="formfield">
        <label>Name:</label>
        <input v-model="form.name" type="text" name="name">
      </div>

      <div class="formfield">
        <label>Website:</label>
        <input v-model="form.website" type="text" name="website" placeholder="https://...">
      </div>

      <div class="formfield">
        <label>Tags:</label>
        <input v-model="form.tags" type="text" name="tags"><br>
        <small>Comma-separated, please.</small>
      </div>

      <button type="submit">+ Add illustrator</button>
    </form>

    <main>
      <section v-if="illustrators" class="illustrators">

        <div
          v-for="(illustrator, i) in allIllustrators"
          ref="illustrators"
          :key="`illustrator--${illustrator.id}`"
          :data-id="illustrator.id"
          :data-is-viewing="viewing.includes(illustrator.id)"
          class="illustrator"
        >
          <form v-if="editing.includes(illustrator.id)" @submit.prevent="updateIllustrator(illustrator.id)">
            <div class="flex">
              <div>
                <div v-if="updates[illustrator.id]" class="formfield">
                  <label>Name:</label>
                  <input v-model="updates[illustrator.id].name" type="text" name="name">
                </div>

                <div v-if="updates[illustrator.id]" class="formfield">
                  <label>Website:</label>
                  <input v-model="updates[illustrator.id].website" type="text" name="website" placeholder="https://...">
                </div>

                <div v-if="updates[illustrator.id]" class="formfield">
                  <label>Tags:</label>
                  <input v-model="updates[illustrator.id].tags" type="text" name="tags"><br>
                  <small>Comma-separated, please.</small>
                </div>
              </div>
              <div class="actions">
                <button type="submit">&check; Update illustrator</button>
                <button type="button" @click="toggleEditMode(illustrator.id)" class="secondary">Cancel</button>
              </div>
            </div>

            <div class="actions">
              <button type="button" @click="removeIllustrator(illustrator.id)" class="remove-button">&times; Remove illustrator</button>
            </div>
          </form>

          <div v-else-if="viewing.includes(illustrator.id)">
            <div class="flex">
              <div>
                <h2 class="name">{{ illustrator.name || '' }}</h2>
                <a :href="illustrator.website || '#'" target="_blank" class="website">{{ illustrator.website || '' }}</a>
              </div>
              <div class="actions">
                <button @click="toggleEditMode(illustrator.id)">&#9998; Edit illustrator</button>
                <button type="button" @click="toggleViewMode(illustrator.id, i)" class="secondary">Close</button>
              </div>
            </div>

            <ul class="tags">
              <li
                v-for="(tag, j) in illustrator.tags"
                :key="j"
              ><a @click="toggleFilter(tag)">{{ tag }}</a></li>
            </ul>

            <form class="image-form" @submit.prevent="addImage(illustrator.id)">
              <input :ref="`imageURL_${illustrator.id}`" type="text" name="image" placeholder="https://...">
              <button type="submit">+ Add image</button>
            </form>

            <div v-if="illustrator.images" class="images">
              <div v-for="(src, z) in illustrator.images" :key="z" class="image">
                <a :href="src" target="_blank"><img :src="src"></a>
                <div class="image-actions">
                  <button @click.prevent="setThumb(illustrator.id, z)">&check; Set as thumb</button>
                  <button @click.prevent="removeImage(illustrator.id, z)" class="secondary">&times; Remove image</button>
                </div>
              </div>

              <div class="spacer"></div>
            </div>

            <div class="actions">
              <button @click="removeIllustrator(illustrator.id)" class="remove-button">&times; Remove illustrator</button>
            </div>
          </div>

          <button type="button" @click.prevent="toggleViewMode(illustrator.id, i)" v-else class="card">
            <div class="thumb">
              <img v-if="illustrator.images && illustrator.images[0]" :src="illustrator.images[0]" />
            </div>

            <h4 class="name">{{ illustrator.name || '' }}</h4>
            <ul class="tags">
              <li
                v-for="(tag, j) in illustrator.tags"
                :key="j"
              >{{ tag }}</li>
            </ul>
        </button>
        </div>
      </section>

      <aside class="tags">
        <div class="tag-stick">
          <h4>Filters:</h4>
          <div v-for="(tag, k) in allTags" :key="k">
            <label>
              <input v-model="filters" type="checkbox" name="filters" :value="tag">
              {{ tag }}
            </label>
          </div>
        </div>
      </aside>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.5.17/dist/vue.js"></script>
  <script src="app.js"></script>
</body>
</html>
